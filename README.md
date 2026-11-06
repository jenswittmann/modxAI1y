# ContentBlocks-Image-ALT-Text-Generator for <img src="https://raw.githubusercontent.com/modxcms/revolution/2.x/manager/templates/default/images/modx-icon-color.svg" width="20"> MODX

Creates alt texts for the images of [ContentBlocks](https://modmore.com/contentblocks/) with
[Claude](https://www.anthropic.com/claude) or [InkluDocs](https://inkludocs.de/api/v1/docs). A button right of the
title field of an image describes the image on click, the editor checks and edits the text. (Formerly modxAI1y.)

https://github.com/user-attachments/assets/f3bda02c-3ae2-465e-88bd-c6afc9fac8d7

## Requirements

- MODX 3, PHP 8.1 or newer, the PHP extension `fileinfo` (and `gd` to scale big images)
- ContentBlocks with image fields of the type "Image with title": the title is the alt text
- an API key of the service you choose: [Anthropic](https://console.anthropic.com/) (`claude`) or InkluDocs (`inkludocs`)

## Install

### As a package

1. Build the package (see [Build the package](#build-the-package)) or take a `cbaltgenerator-<version>.transport.zip`.
2. Copy the zip into `core/packages/` of your MODX site.
3. In the manager: Extras → Installer → "Search Locally for Packages" → Install.
   The package creates the namespace `cbaltgenerator`, the plugin `cbaltgenerator` (event `OnManagerPageBeforeRender`),
   the system settings and copies the files below `core/components/cbaltgenerator` and `assets/components/cbaltgenerator`.
4. Open the system settings (namespace `cbaltgenerator`), choose the service in `cbaltgenerator.provider` and enter its key
   (`cbaltgenerator.apikey` for Claude, `cbaltgenerator.inkludocs_apikey` for InkluDocs).

An update keeps the values of the system settings, so the keys stay.

### From the repository (development)

1. Copy or link `core/components/cbaltgenerator` and `assets/components/cbaltgenerator` into your MODX site.
2. Run `composer install --no-dev` in `core/components/cbaltgenerator`: the Anthropic SDK lives in `vendor/`, which is not committed.
3. Create the namespace, the plugin and the system settings from `_build/config.json`, for example with
   [Git-Package-Management](https://github.com/theboxer/Git-Package-Management).

## Build the package

```bash
composer install --no-dev --working-dir=core/components/cbaltgenerator
php _build/build.transport.php --core=/path/to/modx/core
```

The script only reads the classes and the workspace of a MODX 3 installation, nothing is installed there. It writes
`_packages/cbaltgenerator-<version>.transport.zip` (about 6 MB with the SDK, tests and docs of the dependencies are left
out). `--out=<dir>` changes the target folder. Version and system settings come from `_build/config.json`.

## How it works

1. The plugin adds a button to the image fields in the resource editor (creating and editing resources only).
2. A click posts the image path to the manager connector (`CbAltGenerator\Processors\Mgr\AltText\Generate`).
   The API keys are never part of the page.
3. The processor reads the image from the web root, scales it down (default: longest edge 1568 px), sends it to the chosen
   service and returns one short text.
4. The text lands in the title field. It is a suggestion: please check it before you save.

Only images below the allowed folders (default `upload/`) are read, other hosts and paths with `..` are refused.
The processor needs an active manager session and the permission `save_document`.

## Services

- **claude:** the Anthropic Messages API with the official PHP SDK. Prompt, model, language and maximum length are
  system settings. An image is scaled to at most 1568 px, which is about 1,500 input tokens: with Claude Haiku 5.5 an
  alt text costs a fraction of a cent.
- **inkludocs:** `POST /api/v1/alt-text` with the image as base64 and the page title as `context`. InkluDocs has its own
  rules for the text, so prompt, model and maximum length do not apply. It is billed in credits (5 per image, see the
  InkluDocs documentation).

More services are a class in `src/Providers/` that implements `ProviderInterface`.

## Privacy

The images leave your server and go to the chosen service (Anthropic or InkluDocs). Mention this in your privacy policy.
The generated texts can be wrong: every text needs a check by an editor.

## System settings

| key                                 | value                                                                                                                   |
| ----------------------------------- | ----------------------------------------------------------------------------------------------------------------------- |
| `cbaltgenerator.provider`           | Service: `claude` (default) or `inkludocs`.                                                                             |
| `cbaltgenerator.apikey`             | Anthropic API key (password field). Empty: the environment variable `ANTHROPIC_API_KEY`.                                |
| `cbaltgenerator.model`              | Claude model, default `claude-haiku-5-5` (fast and cheap, enough for short alt texts).                                  |
| `cbaltgenerator.prompt`             | Instruction for the alt texts. Placeholders: `{max_length}`, `{language}`. Empty: the default prompt.                   |
| `cbaltgenerator.language`           | Language of the alt texts as written in the prompt, default `Deutsch`.                                                  |
| `cbaltgenerator.max_length`         | Maximum number of characters (Claude), default 125.                                                                     |
| `cbaltgenerator.max_image_edge`     | Longest image edge in pixels before sending, default 1568.                                                              |
| `cbaltgenerator.allowed_paths`      | Comma separated folders (relative to the web root) the images may come from, default `upload/`.                         |
| `cbaltgenerator.field_queries`      | JSON with the selectors of the ContentBlocks fields that get a button: `[{"field": "…", "image": "…", "target": "…"}]`. |
| `cbaltgenerator.api_base_url`       | Only for proxies or tests, default `https://api.anthropic.com`.                                                         |
| `cbaltgenerator.inkludocs_apikey`   | InkluDocs API key (password field), only for the service `inkludocs`.                                                   |
| `cbaltgenerator.inkludocs_language` | Language code for InkluDocs: `de` (default), `en`, `fr`, `es`, `da`, `sv`.                                              |
| `cbaltgenerator.inkludocs_base_url` | Default `https://inkludocs.de`.                                                                                         |

## Upgrade from modxAI1y 0.1

- The extra is renamed: namespace, folders, plugin and system settings use the prefix `cbaltgenerator` (before: `modxai1y`).
  Install the new package, copy your key into the new setting, then remove the old plugin, namespace and `modxai1y.*` settings.
- MODX 3 only. The OpenAI key is not used any more: enter an Anthropic key (`cbaltgenerator.apikey`) or an InkluDocs key
  (`cbaltgenerator.inkludocs_apikey`).
- The system setting `image_caption_prompt` is now `cbaltgenerator.prompt`.
