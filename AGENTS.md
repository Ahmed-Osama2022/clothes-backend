# AGENTS.md — Clothes Shop Backend

Rules for AI agents (and contributors) working in this repository.

## 1. .editorconfig compliance

Whenever writing or editing any file, follow the settings defined in `.editorconfig`:

- `charset = utf-8`
- `end_of_line = lf`
- `indent_style = space`, `indent_size = 2` (the Laravel/PHP default; keep it)
- `insert_final_newline = true`
- `trim_trailing_whitespace = true`
  - Exception: `*.md` files must NOT trim trailing whitespace.
  - `*.yml` / `*.yaml`: `indent_size = 2`
  - `docker-compose.yml`: `indent_size = 4`

## 2. Protected files

Do **not** modify any of the following without asking the user first:

- Everything under `config/`
- `.env`
- `.env.example`

If you believe a change there is needed, stop and ask.

## 3. Comments in code

Use comments in the code to document any harder-to-follow class or function: complex logic, business rules (e.g. order-time stock re-checks), non-obvious contracts (e.g. the localized `{field}_{locale}` selection with `_en` fallback), or anything that would take a reader extra time to understand. Keep them meaningful and concise.