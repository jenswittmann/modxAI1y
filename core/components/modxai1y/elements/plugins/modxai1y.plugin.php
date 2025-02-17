<?php
/**
 * modxAI1y
 *
 * DESCRIPTION
 *
 * modxAI1y helps you with some AI magic to generate simple image captions in ContentBlocks.
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

# vars
$apiKey = $modx->getOption("modxai1y.apikey");
$imageCaptionPrompt = $modx->getOption(
    "modxai1y.image_caption_prompt",
    null,
    "Write a very short description of the image with a maximum of 100 characters for screen readers."
);
$fieldQueries = $modx->getOption(
    "modxai1y.field_queries",
    null,
    '[
        {
            parent: ".contentblocks-field-gallery-image",
            image: ".contentblocks-field-gallery-image-view img",
            target: ".title"
        },
        {
            parent: ".contentblocks-field-image_with_title",
            image: ".contentblocks-field-image-preview-img",
            target: ".contentblocks-field-image-title-input"
        }
    ]',
    true
);

# JS/CSS code
$code = <<<HTML

<script>
    let apiKey = "$apiKey",
        fieldQueries = $fieldQueries;
    
    let addButtons = (node) => {
        fieldQueries.forEach((fieldQuery) => {
            let fields = node.parentNode.querySelectorAll(fieldQuery.parent);
            fields.forEach((field, i) => {
                let button = document.createElement("button"),
                    anchorName = "--modxai__field" + i,
                    imageUrl = field.querySelector(fieldQuery.image).src,
                    target = field.querySelector(fieldQuery.target);
                button.innerText = "AI";
                button.classList.add("modxai__button");
                button.style.positionAnchor = anchorName;
                button.addEventListener("click", (e) => {
                    askAI(imageUrl, target, button);
                });
                field.appendChild(button);
                target.style.anchorName = anchorName;
            });
        });    
    };
    
    let askAI = (imageUrl, target, button) => {
        if (!apiKey) {
            alert("Please enter an API key. You can get a free one at platform.openai.com.");
            return;
        }
        button.classList.add("modxai__loading");          
        fetch(imageUrl)
            .then((response) => response.blob())
            .then((response) => {
                const reader = new FileReader();
                reader.onloadend = () => {
                    fetch("https://api.openai.com/v1/chat/completions", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": "Bearer " + apiKey
                        },
                        body: JSON.stringify({
                            model: "gpt-4o-mini",
                            messages: [
                                {
                                    role: "user",
                                    content: [
                                        {
                                            "type": "text", 
                                            "text": "$imageCaptionPrompt"
                                        },
                                        {
                                            "type": "image_url",
                                            "image_url": {
                                                "url": reader.result,
                                            },
                                        }
                                    ]
                                }
                            ]
                        })
                    })
                        .then((response) => response.json())
                        .then((response) => {
                            target.value = response.choices[0].message.content;
                            button.classList.remove("modxai__loading");
                        }); 
                };
                reader.readAsDataURL(response);
            });
    };
    
    const observer = new MutationObserver((mutationList, observer) => {
        for (const mutation of mutationList) {
            if (mutation.type === "childList") {
                mutationList.forEach((item) => {
                    item.addedNodes.forEach((node) => {
                        addButtons(node.nextSibling);
                    });
                });
            }
        }
    });
    
    window.addEventListener("load", () => {
        setTimeout(() => {
            let parent = document.getElementById("contentblocks");
            addButtons(parent);
            observer.observe(parent, {
                attributes: false,
                childList: true,
                subtree: true
            });
        }, 1000);
    });
</script>

<style>
 .modxai__button {
     cursor: pointer;
     position: absolute;
     top: anchor(top);
     right: anchor(right);
     bottom: anchor(bottom);
     overflow: hidden;
     text-indent: -4em;
     margin: .3em;
     background: #eee url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><rect x="32" y="56" width="192" height="160" rx="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><rect x="72" y="144" width="112" height="40" rx="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="148" y1="144" x2="148" y2="184" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="108" y1="144" x2="108" y2="184" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="128" y1="56" x2="128" y2="16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><circle cx="84" cy="108" r="12"/><circle cx="172" cy="108" r="12"/></svg>') no-repeat center;
     background-size: 70%;
     border: 0;
     border-radius: 50%;
     aspect-ratio: 1/1;
 }
 
 .modxai__button:hover {
     background-color: #ccc;
 }
 
 .modxai__loading {
     animation: modxai__loading 1s infinite;
 }
 
 @keyframes modxai__loading {
     0% {
         transform: rotate(0deg);
     }
     100% {
         transform: rotate(360deg);
     }
 }
</style>

HTML;

# register on event
switch ($modx->event->name) {
    case "OnManagerPageBeforeRender":
        $modx->regClientStartupHTMLBlock($code);
        break;
}
