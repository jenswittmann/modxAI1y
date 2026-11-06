/**
 * modxAI1y – "create alt text" buttons for the image fields of ContentBlocks.
 *
 * Window.Modxai1y (set by the plugin): { connectorAction, fieldQueries, lexicon }.
 * The browser never sees an API key: it posts the image path to the MODX connector, the processor
 * calls Claude on the server and returns the text.
 */
(function () {
    'use strict';

    var config = window.Modxai1y;
    if (!config || !window.MODx || !Array.isArray(config.fieldQueries)) {
        return;
    }
    var lex = config.lexicon || {};

    // inputs hold the path in value, images in src
    var readImage = function (field, selector) {
        var el = field.querySelector(selector);
        if (!el) {
            return '';
        }
        return (el.tagName === 'IMG' ? el.getAttribute('src') : el.value) || '';
    };

    // ContentBlocks reads the input on save, other scripts listen to events
    var setValue = function (input, value) {
        input.value = value;
        ['input', 'change'].forEach(function (type) {
            input.dispatchEvent(new Event(type, { bubbles: true }));
        });
    };

    var pageTitle = function () {
        var input = document.getElementById('modx-resource-pagetitle');
        return input ? input.value : '';
    };

    var setup = function (field, query, target) {
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'modxai1y-button';
        button.title = lex.buttonTitle || '';
        button.textContent = lex.button || 'Alt text';

        var status = document.createElement('span');
        status.className = 'modxai1y-status';
        status.setAttribute('role', 'status');

        var wrap = document.createElement('div');
        wrap.className = 'modxai1y-wrap';
        wrap.appendChild(button);
        wrap.appendChild(status);
        target.insertAdjacentElement('afterend', wrap);
        // the field is alone in its container: input and button share a row, the status goes below
        if (target.parentNode.children.length === 2) {
            target.parentNode.classList.add('modxai1y-row');
        }

        var finish = function (message) {
            button.disabled = false;
            button.removeAttribute('aria-busy');
            status.textContent = message;
        };

        button.addEventListener('click', function () {
            var image = readImage(field, query.image);
            if (!image) {
                status.textContent = lex.noImage || '';
                return;
            }
            if (target.value.trim() && !window.confirm(lex.overwrite || 'Replace?')) {
                return;
            }
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
            status.textContent = lex.busy || '';

            MODx.Ajax.request({
                url: MODx.config.connector_url,
                params: {
                    action: config.connectorAction,
                    namespace: 'modxai1y',
                    image: image,
                    context: pageTitle(),
                },
                listeners: {
                    success: {
                        fn: function (response) {
                            setValue(target, response.object.alt);
                            finish(lex.done || '');
                        },
                    },
                    failure: {
                        fn: function (response) {
                            finish((response && response.message) || lex.error || '');
                        },
                    },
                },
            });
        });
    };

    var scan = function () {
        config.fieldQueries.forEach(function (query) {
            document.querySelectorAll(query.field).forEach(function (field) {
                if (field.dataset.modxai1y) {
                    return;
                }
                var target = field.querySelector(query.target);
                if (!target) {
                    return;
                }
                field.dataset.modxai1y = '1';
                setup(field, query, target);
            });
        });
    };

    // ContentBlocks builds and rebuilds its fields in the browser: scan again when the DOM changes
    var scheduled = false;
    new MutationObserver(function () {
        if (!scheduled) {
            scheduled = true;
            window.requestAnimationFrame(function () {
                scheduled = false;
                scan();
            });
        }
    }).observe(document.documentElement, { childList: true, subtree: true }); // the script loads in <head>, before <body> exists

    scan();
})();
