# OPENCODE_WORK.md

Running reference of what the AI assistant changed in this repository, so the
chat output can stay short. Newest work is appended at the bottom.

**How to use:** after each task the assistant adds one dated section here and
replies with a one-line summary pointing to this file.

---

## Session 1

### 1. Project rules — `AGENTS.md`
Created the agent rules file with four rules:
- **Rule 1:** follow `.editorconfig` on every write (utf-8, LF, 2-space indent,
  final newline, no trailing whitespace; `*.md` keeps trailing whitespace).
- **Rule 2:** do not touch `config/`, `.env`, `.env.example` without asking first.
- **Rule 3:** comment harder classes/functions (business rules, non-obvious contracts).
- **Rule 4:** scaffold models with `php artisan make:model <Name> --all --api`
  (JSON-only API).

### 2. Schema reference — `DATABASE.md`
Wrote the table list from `BACKEND_HANDOFF.md` as a note file (git-ignored via
`.gitignore`).

### 3. Database chosen
Confirmed the project targets a relational DB (MySQL) — schema from the handoff:
`users, categories, products, product_sizes, reviews, promo, testimonials,
orders, order_items` (+ `images` we added).

### 4. Polymorphic `images` table
- Added `database/migrations/..._create_images_table.php`:
  `id`, `morphs('imageable')` (`imageable_type` + `imageable_id` + index),
  `url`, `sort_order` (unsigned smallint, default 0), timestamps.
- Removed the redundant `image` string column from
  `..._create_categories_table.php` (category gets images via the morph relation).

### 5. Image model + setup
- `app/Models/Image.php` — `$fillable` (`url`, `sort_order`) + `imageable(): MorphTo`.
- `app/Models/Category.php` — added `$fillable` and
  `images(): MorphMany` (`morphMany(Image::class, 'imageable')->orderBy('sort_order')`).
- `database/factories/ImageFactory.php` — `url` + `sort_order`; owner attached at
  call time via `->for($model, 'imageable')`.
- Scaffolded (minus a duplicate migration): `ImageController` (API),
  `StoreImageRequest`/`UpdateImageRequest`, `ImagePolicy`, `ImageSeeder`.

### 6. Soft deletes — PLANNED, NOT APPLIED
Plan was to add `$table->softDeletes()` + `SoftDeletes` trait to Category and
Product, and rebuild with `migrate:fresh` (user chose "edit original + refresh").
This was **not** executed — verified later: no `deleted_at` and no `SoftDeletes`
trait anywhere. Still to do.

### 7. `Order` + `OrderItem` models
User added the migrations/models; assistant added the model code:
- `app/Models/Order.php` — `$fillable` (`user_id`, `status`, `total_amount`),
  `total_amount` cast `decimal:2`, `user(): BelongsTo`, `items(): HasMany`.
- `app/Models/OrderItem.php` — `$fillable` (`order_id`, `product_id`, `size`,
  `quantity`, `unit_price`), casts (`quantity` int, `unit_price` decimal:2),
  `order(): BelongsTo`, `product(): BelongsTo`.

### 8. Migration fixes
- `..._create_products_sizes_table.php`: removed a stray dead line `('quantity');`.
- `..._create_orders_table.php`: `foreignIdFor('user_id')` → `foreignId('user_id')`
  (passing a column name to `foreignIdFor()` throws), and made `user_id`
  `nullable()->nullOnDelete()` so guests can check out (handoff §5).
- `..._create_order_items_table.php`: same fix for `order_id` / `product_id`.

### 9. API-layer work reverted
An order-placement controller/validation/routes were written, then removed at the
user's request so we stay on models + migrations. Reverted files:
`OrderController.php`, `StoreOrderRequest.php`, `CategoryFactory.php`,
`ProductFactory.php`, `OrderFactory.php`, `OrderItemFactory.php`, `routes/api.php`.
(`ImageController`/`ImagePolicy`/requests scaffolding remains.)

### 10. Factories + seeders
- `app/Models/ProductSize.php`: added `HasFactory`.
- `database/factories/ProductSizeFactory.php`: new (`product_id`, `size`, `quantity`;
  0 = sold out).
- Filled: `CategoryFactory`, `ProductFactory`, `OrderFactory`, `OrderItemFactory`
  (`ImageFactory` done in step 5; `UserFactory` already complete).
- Seeders:
  - `CategorySeeder` — the 3 required slugs, idempotent `updateOrCreate`.
  - `ProductSeeder` — 6 products per category + one `product_sizes` row per XS–XL.
  - `ImageSeeder` — 2 images per category, 3 per product (polymorphic).
  - `OrderSeeder` — orders for users + 2 guest orders (`user_id = null`).
  - `OrderItemSeeder` — line items, snapshots `unit_price`, recomputes `total_amount`.
  - `DatabaseSeeder` — wired in order: Category → Product → Image → Order → OrderItem.
- Verified via `php artisan migrate:fresh --seed`:
  11 users, 3 categories, 18 products, 90 sizes, 60 images, 12 orders (2 guest),
  17 items.

### 11. Auto slug + route binding — `HasSlug`
- Added `app/Models/Concerns/HasSlug.php`:
  - `bootHasSlug()` generates a unique `slug` from `name` on create when none is
    given (collisions get `-2`, `-3`, …).
  - `getRouteKeyName()` returns `slug`, so API routes bind by slug.
- `Category` and `Product` now `use HasSlug;`.
- Verified: `Summer Essentials` → `summer-essentials`, duplicate → `summer-essentials-2`,
  explicit slug preserved, route key = `slug`.

### 12. User roles — `roles` + `role_user` (many-to-many)
Decision: roles are many-to-many (not a `users.role` column). Guests remain
users-less (they only exist as `orders.user_id = null`), so no guest role row.
- `database/migrations/2026_10_11_225310_create_roles_table.php`:
  `id`, `name` (unique), timestamps.
- `database/migrations/2026_10_11_225311_create_role_user_table.php`:
  pivot `id`, `user_id` FK, `role_id` FK (both cascade), unique
  (`user_id`, `role_id`), timestamps.
  - Note: this file was renamed from `..._225310_...` to `..._225311_...`
    because `create_role_user` sorts before `create_roles` at the same
    timestamp, which would have run the pivot before the `roles` table existed.
- `app/Models/Role.php` — scaffolded per rule 4
  (`make:model Role --all --api`); `$fillable = ['name']`, `users(): BelongsToMany`.
- `app/Models/User.php` — added `roles(): BelongsToMany`, `hasRole(string): bool`,
  `isAdmin(): bool`.
- `RoleFactory` (unique name), `RoleSeeder` (idempotent `firstOrCreate` for
  `admin`, `customer`).
- `DatabaseSeeder` — seeds roles first, creates an admin
  (`admin@example.com`) + 10 customers + `test@example.com`, attaching roles.
- Scaffolded with the model (rule 4): `RoleController`, `RolePolicy`,
  `StoreRoleRequest`/`UpdateRoleRequest` (stubs, no routes yet).
- Verified via `migrate:fresh --seed`: roles `admin,customer`, 12 pivot rows,
  admin `isAdmin() = true`, customer `isAdmin() = false`.

---

## Session 2 — API reference page

### 1. Docs rule — `AGENTS.md` § 7 (+ § 6 exception)
Every API endpoint must now be documented in the same change. Single source of
truth is `docs/api/v1.php`; § 6 gained the one approved web-UI exception (the
internal docs page), kept separate from storefront work.

### 2. Reference content — `docs/api/v1.php`
Structured spec: meta (title/version/base URL/description), cross-cutting
conventions (localization, slugs, money, timestamps, errors, pagination), two
groups (`Catalog` with the 4 live endpoints, `Authentication` with `GET /api/user`)
and a `planned` list for the stubbed/unregistered routes. Examples were copied
from real responses of the running API.

### 3. Web route + controller + view
- `GET /docs/api` → `App\Http\Controllers\Docs\ApiDocsController@index`
  (`app/Http/Controllers/Docs/ApiDocsController.php`), which `require`s the spec
  and returns `resources/views/docs/api.blade.php`.
- `GET /docs` → 302 to `/docs/api`.
- View: single self-contained page (inline CSS, no build step, no external
  assets) with sticky sidebar, method/status badges, parameter tables, and
  pretty-printed JSON examples.

### 4. Verified
- `php artisan route:list --path=docs` shows both routes.
- `curl /docs/api` → 200 (~30 KB); `/docs` → 302.
- All 8 JSON example blocks decode as valid JSON after HTML-entity decoding.
- `php artisan test` still passes (2 skeleton tests).

### 5. Foldable endpoints
Each endpoint card is now a native `<details>/<summary>` accordion — collapsed by
default, opens only on click (keyboard accessible, no JS for the fold). A small
inline script adds a 250ms `cubic-bezier(0.4, 0, 0.2, 1)` height/fade/slide
transition for expand and collapse (instant under `prefers-reduced-motion`), and
opens the card targeted by a sidebar anchor (`#get-products-product`) so deep
links still land on an expanded endpoint. The chevron animates over the same
250ms.

---

## Session 3 — Real catalog images

### 1. `database/seeders/ImageSeeder.php`
Replaced the Faker placeholder URLs with a curated pool of **60 Unsplash
clothing/fashion photos** (`public const PHOTOS`), every link HTTP-verified and
visually reviewed. Photos are handed out sequentially: 2 per category, 3 per
product, `sort_order` 1..n so the first image is a stable hero. 60 links map
exactly onto 3×2 + 18×3 = 60 slots, so every gallery is distinct.

### 2. `database/factories/ImageFactory.php`
`url` now comes from `ImageSeeder::PHOTOS` (random element), so factory-made
images are never placeholders either — one source of truth for the pool.

### 3. `images.url` → `images.image_url` (aligned end to end)
Per the owner's decision, the column was renamed **in the original create
migration** (`$table->string('image_url')`) instead of adding a rename
migration, then the DB was rebuilt. Updated with it: `ImageFactory`
(`image_url`), `ImageSeeder` (`image_url`, back to plain `create()` since the
allow-list now matches), and `ImageResource` (`'url' => $this->image_url`, so the
public JSON key stays `url`). `docs/api/v1.php` examples now use real Unsplash
links instead of the old placeholder URLs.

### 4. Re-seeded (one-off, user-approved)
`php artisan migrate:fresh --seed` — 60 images, 60 distinct URLs, 0 placeholders,
2 images per category, 3 per product; other counts unchanged (12 users,
3 categories, 18 products, 360 variants, 32 orders, 48 items). Verified through
the API: `GET /api/v1/products/classic-white-tee` and
`GET /api/v1/categories/signature-collection` return the Unsplash URLs.

### 6. `ImageResource` aligned with the other resources
Image objects were the only snake_case payload (`sort_order`) and lacked
timestamps. Now: `id`, `url`, `sortOrder`, `createdAt`, `updatedAt` — same
camelCase + ISO-8601 shape as Category/Product/Variant. The 5 image examples in
`docs/api/v1.php` were updated to match (including a `+02:00` offset, since the
seeded rows are now in November). `Report.md` §3.4/§7.5 updated.

---

## Session 4 — Orders API

### 1. Model
`Order` gained `STATUSES` (mirrors the DB enum) and `items()` now orders by `id`,
so line order in the JSON is stable.

### 2. Validation — `app/Http/Requests/`
- `StoreOrderRequest`: `items` (array, min 1), `items.*.product_variant_id`
  (exists + `distinct`), `items.*.quantity` (1–99). **Prices are not accepted** —
  they are snapshotted from the DB. A custom message explains the `distinct` rule.
- `UpdateOrderRequest`: `status` must be one of `Order::STATUSES`; nothing else is
  mutable after checkout.

### 3. Checkout transaction — `OrderController@store`
Reads the variants, rejects inactive ones, then decrements with a **guarded
`UPDATE ... WHERE quantity >= ?`** (the atomic oversell gate — no lock, no
read-then-write race), snapshots `unit_price` (variant price, falling back to the
product price), sums the total **in cents** to avoid float drift, and writes the
order + items inside `DB::transaction`. Any shortfall throws
`OrderConflictException` → transaction rolls back → `409 {message, status}`.
Guests get `user_id = null`; a token/session links the order.

### 4. Resources
`OrderResource`: `id, status, totalAmount, itemsCount, customer{id,name,email}|null,
items[], createdAt, updatedAt`. `OrderItemResource`: `id, productId,
productVariantId, quantity, unitPrice, lineTotal, product{id,slug,name,title},
variant{id,size,color,colorHex,sku}` — nested snapshots stay compact, and the
product title / variant color honour `?lang=`.

### 5. Policy + routes
`OrderPolicy` is real: admin lists/updates/deletes, owner-or-admin views (guest
orders are admin-only). `Gate::authorize()` runs in every action.
`Route::apiResource('orders')->middlewareFor(['index','show','update','destroy'],
'auth:sanctum')` — checkout stays public for guests.

### 6. `bootstrap/app.php` — JSON-only API errors
Guests hitting a protected route without `Accept: application/json` used to 500
with `Route [login] not defined` (the default `redirectGuestsTo` calls
`route('login')`, which does not exist here). Now guests under `/api/*` get no
redirect and `shouldRenderJsonWhen` forces JSON rendering, so every API error
keeps the `{ message, status }` shape. Verified: `GET /api/v1/orders`,
`GET /api/v1/orders/1`, `GET /api/user` all return `401
{"message":"Unauthenticated."}`.

### 7. Docs
New **Orders** group in `docs/api/v1.php`: `GET/POST /orders`,
`GET/PUT|PATCH/DELETE /orders/{order}` with parameters (`lang`, body fields),
status codes (200/201/204/401/403/404/409/422) and accurate examples; the three
order rows were removed from `planned` (replaced by `/me/orders` and
`/orders/{order}/cancel`). The blade now prints "No response body." when a status
has `example => null` (used by 204).

### 8. Tests — `tests/Feature/OrderEndpointTest.php` (20 tests)
Guest checkout (total `89.48`, stock decremented, `customer: null`), authenticated
checkout linked to the account, `409` on insufficient stock (nothing written),
`409` on inactive variant, `422` for empty/unknown/duplicate/zero-quantity lines,
admin list vs customer `403` vs guest `401`, owner view vs other-customer `403`,
admin view of a guest order, JSON `404` payload, admin status update vs customer
`403`, invalid status `422`, admin delete `204` vs customer `403`.

### 9. Verified
- `php artisan test` → **29 passed** (121 assertions) on sqlite `:memory:`.
- Live smoke on MySQL (read-only): admin token → `GET /api/v1/orders?lang=ar`
  returns 27 orders with localized product title (`طقم استرخاء من قطعتين`) and
  color (`أبيض`), `itemsCount`, nullable `customer`; `GET /api/v1/orders/1` → 200.
  The temporary token was deleted afterwards.
- `/docs/api` renders 200 with the Orders group; unauthenticated calls return 401
  JSON.

---

## Open items / next steps
- Apply soft deletes to `Category` and `Product` (step 6) — not done yet.
- Auth endpoints (`/api/v1/auth/login|register|logout`) — required before the
  token-guarded order routes are usable by clients (orders work today only with a
  manually issued Sanctum token).
- `GET /me/orders` (customer order history) and `POST /orders/{order}/cancel`
  (owner, `pending`/`processing` only) — documented as planned.
- Per-variant stock endpoint (`GET /products/{id}/stock` → `{product_id, sizes[]}`)
  and an aggregated `/api/home` feed — still missing.
- Seed the real catalog: the handoff's 18 products + exact Arabic strings still
  need to replace the factory-generated demo data.
- `reviews`, `promo`, `testimonials` tables/seeders not created yet.
- Pending decision: rename the About CMS model `Page` → `About` (tables not
  migrated yet, so the rename is still free).
