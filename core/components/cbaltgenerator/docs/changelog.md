## ContentBlocks-Image-ALT-Text-Generator 0.2.0-beta

- renamed from modxAI1y to ContentBlocks-Image-ALT-Text-Generator: namespace, folders, plugin and system settings use the prefix `cbaltgenerator`
- Claude (Anthropic Messages API, default model `claude-haiku-5-5`) instead of OpenAI
- plain `fetch` and DOM in the manager script, no ExtJS; the connector URL and the token come from the plugin
- settings `use_context` and `inkludocs_use_context` (the page title as a hint, off by default for InkluDocs); texts of services other than Claude are shortened to whole sentences up to `max_length`
- second service InkluDocs (`cbaltgenerator.provider` = `inkludocs`), services are classes in `src/Providers/`
- the API call runs on the server (manager connector processor), the API key is no longer part of the manager page
- MODX 3 only (namespace, class based processor, lexicon in English and German)
- images are read from the allowed folders only (`cbaltgenerator.allowed_paths`), scaled down before they are sent
- accessible text button (`aria-busy`, status message) instead of an icon-only button, errors are shown to the editor
- new system settings: `model`, `language`, `max_length`, `max_image_edge`, `allowed_paths`, `api_base_url`
- renamed system setting `image_caption_prompt` to `prompt` (placeholders `{max_length}`, `{language}`)

## modxAI1y 0.1.0

Released on 2025-01-05

- initial release
