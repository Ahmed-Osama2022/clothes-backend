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


## Open items / next steps
- Apply soft deletes to `Category` and `Product` (step 6) — not done yet.
- Build the actual API layer later: controllers, form requests, routes, the
  order-placement transaction with per-size stock re-check returning `409`
  (`{ "message": "Size «M» is out of stock" }`).
- Seed the real catalog: the handoff's 18 products + exact Arabic strings still
  need to replace the factory-generated demo data.
- `reviews`, `promo`, `testimonials` tables/seeders not created yet.
