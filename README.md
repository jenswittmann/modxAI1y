# modxAI1y for <img src="https://raw.githubusercontent.com/modxcms/revolution/2.x/manager/templates/default/images/modx-icon-color.svg" width="20"> MODX

modxAI1y creates alt texts for images in ContentBlocks with [Claude](https://www.anthropic.com/claude) or
[InkluDocs](https://inkludocs.de/api/v1/docs). A button next to the title field of an image describes the image on click;
the editor checks and edits the text.

https://github.com/user-attachments/assets/f3bda02c-3ae2-465e-88bd-c6afc9fac8d7

## Requirements

- MODX 3, PHP 8.1 or newer
- ContentBlocks with image fields of the type "Image with title" (the title is the alt text)
- an [Anthropic API key](https://console.anthropic.com/) (service `claude`) or an InkluDocs API key (service `inkludocs`)

## Install

1. Install the extra (package or files below `core/components/modxai1y` and `assets/components/modxai1y`).
2. When you work from the repository: run `composer install --no-dev` in `core/components/modxai1y` (the Anthropic SDK lives in `vendor/`, which is not committed).
3. Create the namespace `modxai1y`, the plugin `modxai1y` (event `OnManagerPageBeforeRender`) and the system settings from `_build/config.json` (the package does this for you).
4. Choose the service in `modxai1y.provider` (`claude` is the default, or `inkludocs`) and enter its API key: `modxai1y.apikey` (Claude) or `modxai1y.inkludocs_apikey` (InkluDocs).

## How it works

1. The plugin adds a button to the image fields in the resource editor.
2. A click posts the image path to the manager connector (`Modxai1y\Processors\Mgr\AltText\Generate`). The key is never part of the page.
3. The processor reads the image from the web root, scales it down (default: longest edge 1568 px), sends it to the chosen service (Anthropic Messages API or the InkluDocs API) and returns one short text.
4. The text lands in the title field. It is a suggestion: please check it before you save.

Only images below the allowed folders (default `upload/`) are read, other hosts and paths with `..` are refused.
The images leave your server and go to the chosen service (Anthropic or InkluDocs): mention this in your privacy policy when you use the extra.

## System Settings

| key                         | value                                                                                                                       |
| --------------------------- | --------------------------------------------------------------------------------------------------------------------------- |
| modxai1y.provider           | Service: `claude` (default) or `inkludocs`.                                                                                 |
| modxai1y.apikey             | Your Anthropic API key (password field). Empty: the environment variable `ANTHROPIC_API_KEY`.                               |
| modxai1y.model              | Claude model, default `claude-haiku-5-5` (fast and cheap, enough for short alt texts).                                      |
| modxai1y.prompt             | Instruction for the alt texts. Placeholders: `{max_length}`, `{language}`. Empty: the default prompt.                       |
| modxai1y.language           | Language of the alt texts as written in the prompt, default `Deutsch`.                                                      |
| modxai1y.max_length         | Maximum number of characters, default 125.                                                                                  |
| modxai1y.max_image_edge     | Longest image edge in pixels before sending, default 1568.                                                                  |
| modxai1y.allowed_paths      | Comma separated folders (relative to the web root) the images may come from, default `upload/`.                             |
| modxai1y.field_queries      | JSON with the selectors of the ContentBlocks fields that get a button: `[{"field": "…", "image": "…", "target": "…"}]`.     |
| modxai1y.api_base_url       | Only for proxies or tests, default `https://api.anthropic.com`.                                                             |
| modxai1y.inkludocs_apikey   | Your InkluDocs API key (password field), only for the service `inkludocs`.                                                  |
| modxai1y.inkludocs_language | Language code for InkluDocs: `de` (default), `en`, `fr`, `es`, `da`, `sv`.                                                  |
| modxai1y.inkludocs_base_url | Default `https://inkludocs.de`.                                                                                             |

## Services

- **claude:** the Anthropic Messages API with your prompt, model, language and maximum length. An image is scaled to at most 1568 px, which is about 1,500 input tokens, with Claude Haiku 5.5 an alt text costs a fraction of a cent.
- **inkludocs:** `POST /api/v1/alt-text` with the image as base64 and the page title as `context`. InkluDocs has its own rules for the text, so prompt, model and maximum length do not apply. It is billed in credits (5 per image, see the InkluDocs documentation).

Prompt, model, language and maximum length belong to `claude`. More services can be added as a class in `src/Providers/` that implements `ProviderInterface`.

## Upgrade from 0.1

- MODX 3 only. The OpenAI key is not used any more: enter an Anthropic key.
- The system setting `image_caption_prompt` is now `prompt`.
