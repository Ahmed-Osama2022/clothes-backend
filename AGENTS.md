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

## 4. Generating models

This is a pure JSON API (Laravel 11). Whenever scaffolding a new model, use:

```bash
php artisan make:model <Name> --all --api
```

- `--all` generates the model + migration + factory + seeder + controller + policy (+ form request).
- `--api` generates an **API resource controller** (no `create`/`edit` web methods) — required to match the project's JSON-only contract.

## 5. API not-found responses

For single-item endpoints (`show`, and any explicit lookup before `update`/`destroy`), do **not** rely on the framework's default route-model-binding 404. Look the record up explicitly and return a JSON body that names the resource and echoes the requested identifier:

```php
$item = Product::with(['category', 'images', 'variants'])
  ->where('slug', $product)
  ->first();

if (! $item) {
  return response()->json([
    'message' => "Product ($product) not found, try to refresh the product list or use the correct slug name.",
    'status' => 404,
  ], 404);
}

return new ProductResource($item);
```

- Use HTTP **404** and include a `status` field equal to the status code.
- Keep the message resource-specific (`Product`, `Category`, …) and include the requested slug.
- Use this same payload shape for every resource so clients can handle errors uniformly.
