## modxAI1y 0.2.0-beta

- Claude (Anthropic Messages API, default model `claude-haiku-5-5`) instead of OpenAI
- second service InkluDocs (`modxai1y.provider` = `inkludocs`), services are classes in `src/Providers/`
- the API call runs on the server (manager connector processor), the API key is no longer part of the manager page
- MODX 3 only (namespace, class based processor, lexicon in English and German)
- images are read from the allowed folders only (`modxai1y.allowed_paths`), scaled down before they are sent
- accessible text button (`aria-busy`, status message) instead of an icon-only button, errors are shown to the editor
- new system settings: `model`, `language`, `max_length`, `max_image_edge`, `allowed_paths`, `api_base_url`
- renamed system setting `image_caption_prompt` to `prompt` (placeholders `{max_length}`, `{language}`)

## modxAI1y 0.1.0

Released on 2025-01-05

- initial release
