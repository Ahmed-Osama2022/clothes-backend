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

## 6. No front-end work (for now)

This project is backend-only: a pure JSON API consumed by external front-ends.

Do **not** create or touch anything front-end related — no Blade views, no Tailwind/CSS, no JS bundles, no Vite assets, no Inertia/Livewire. If a task seems to require front-end output, stop and ask the user first; the answer is "no need for now" unless they explicitly say otherwise.

The `resources/` directory and Vite wiring exist only from the default Laravel skeleton — treat them as untouched boilerplate, not as something to build on.

**One approved exception:** the internal API reference page (`GET /docs/api`). It is developer documentation served by `App\Http\Controllers\Docs\ApiDocsController` through `resources/views/docs/api.blade.php`, with self-contained inline CSS and no build step. Do not extend it into a storefront UI.

## 7. Document every API endpoint

Every endpoint — new, changed, or removed — must be documented in the same change.

- The single source of truth is `docs/api/v1.php` (one entry per endpoint: method, path, summary, description, auth, parameters, and every response status with a copy-paste accurate JSON example).
- The page at `GET /docs/api` renders that file; there is no separate HTML to edit for content.
- Use standard API-doc conventions: one entry per route, parameters listed with `in` (path/query/body), type, required flag, and status-coded responses (`200`, `404`, `422`, …).
- Move an entry out of the `planned` list into its group only when the endpoint actually works — never document a stub as if it were live.
- Keep examples in sync with the API Resources (`app/Http/Resources/Api/V1`); if a field is added or renamed, update the example in the same commit.
