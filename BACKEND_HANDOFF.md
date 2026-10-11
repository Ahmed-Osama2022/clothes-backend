# BACKEND HANDOFF — Clothes Shop Frontend

**Date:** Sunday, October 11, 2026
**Prepared for:** The Laravel (backend) developer
**Frontend stack:** Vue 3 + Vite + Tailwind CSS v3 SPA · Pinia · vue-router · vue-i18n · axios (Sanctum-ready)
**Reference file:** `apis.md` (kept at the same project root — the living wire-contract)

---

## 1. Project overview

A bilingual **clothes e‑commerce storefront** (English / Arabic, RTL). It is currently a fully
working **frontend with placeholder data** — the backend does not exist yet. Every piece of
remote data is mocked in `src/data/*.js`, and two fake endpoints (`src/api/home.js` and
`src/api/stock.js`) already return the **exact JSON shapes the Laravel API must send**, so the
backend can be dropped in with zero frontend changes.

### Frontend routes (pages)
| Route | Page |
|---|---|
| `/` | Home — hero slider, featured categories, best sellers, promo countdown, testimonials |
| `/shop` | Full catalog / shop grid (filter + sort + pagination-ready) |
| `/category/:slug` | Category listing (`signature-collection`, `mid-season-sale`, `best-sellers`) |
| `/product/:id` | Product detail — **size selector + per-size stock check**, qty, add to cart, related |
| `/login` · `/register` | Auth pages (live validation, Sanctum-ready) |
| `/about` | Static about page |
| `/testcard` | Dev/test component page |
| `/*` | 404 |

---

## 2. What is built so far (current state)

1. **Localization (EN/AR + RTL)** — vue-i18n for UI chrome; **data-tier content is locale-keyed** (`{ en, ar }` objects resolved client-side, which is exactly how the backend will serve localized rows). `<html lang/dir>` flips to `rtl` for Arabic on the frontend — the backend does nothing for RTL.
2. **Product page size selector** — XS/S/M/L/XL buttons fed by `GET /api/products/{id}/stock`. Out‑of‑stock sizes render disabled/struck‑through; a valid size is required before add‑to‑cart; the chosen size is stored **on the cart line** (cart lines are unique by `product_id + size`).
3. **Shopping cart** — Pinia store persisted in `localStorage` (`cart_key`), drawer UI in Navbar with per‑size lines, qty steppers, remove, total.
4. **Auth forms (Sanctum-ready)** — `src/api/auth.js` + `src/stores/auth.js`, axios `withCredentials` SPA cookie mode with an optional Bearer-token fallback. **Not yet wired to a live API.**
5. **Loading system** — Tailwind shimmer skeletons for every async surface + NProgress route bar. The frontend expects real calls to replace mocks by swapping only `src/api/*JS` imports.
6. **Toasts** — global toast/notification store (`src/stores/toast.js`).

---

## 3. How the frontend consumes data today

| Data | Local source (today) | Will come from |
|---|---|---|
| Categories + navbar | `src/data/catalog.js` → fake `src/api/home.js` | `GET /api/home?locale=` |
| Products (shop/category/product/related) | `src/data/catalog.js` | `GET /api/products`, `/api/products/{id}`, `/related` |
| Per-size stock | fake `src/api/stock.js` | `GET /api/products/{id}/stock` |
| Promo + testimonials | `src/data/home.js` | `GET /api/home?locale=` |
| Auth user | `src/api/auth.js` (unused mock) | Sanctum auth endpoints |

### axios client behaviour the backend must respect (`src/api/client.js`)
- Base URL from `VITE_API_URL` (falls back to `http://localhost:3200` — see project `.env`).
- `withCredentials: true` — expects Sanctum **httpOnly cookie** session auth; `X-XSRF-TOKEN` is forwarded manually from the `XSRF-TOKEN` cookie on every request.
- The response interceptor **unwraps** `response.data`; errors are normalized to `Error(message)`. The frontend therefore reads `{ data: ... }` from the **JSON body** — i.e. a real `api.get('/products/1/stock')` resolves to the body object `{ data: { ... } }` and components unwrap one more level with `.data`.
- **`401`** → interceptor clears session and redirects to `/login`.
- **`419`** (CSRF mismatch) → interceptor re-hits `/sanctum/csrf-cookie` and retries once automatically.

---

## 4. Localization contract (most important rule)

- The backend returns **already-translated** strings. The frontend applies **no** translation to data content.
- Every data endpoint accepts `?locale=en|ar` (default `en`, fallback `en`).
- DB columns follow `{field}_{locale}`: e.g. `name_en` / `name_ar`, `title_en` / `title_ar`, `description_ar` / `description_en`… Laravel selects the column for the requested `locale`, falling back to `_en`.
- **Locale-independent fields** live outside the locale pair: `price`, `slug`, `id`, `image`, `size`, `count`, `rating`, `ends_at`, `avatar`.
- `product-sizes` are `XS/S/M/L/XL` — language-independent, sent once (no locale param on the stock endpoint).

---

## 5. Models (Laravel migrations to create)

### `users` (Sanctum)
`id`, `name`, `email` (unique), `password`, `remember_token`, timestamps.

### `categories`
| Column | Type / notes |
|---|---|
| `id` | PK |
| `slug` | unique — **must match frontend slugs exactly** |
| `title_en`, `title_ar` | localized label |
| `eyebrow_en`, `eyebrow_ar` | small accent line |
| `description_en`, `description_ar` | optional |
| `image_url` | hero/tile image |
| `sort_order` | int |

Required slugs (seed them exactly, the frontend URLs depend on them):
`signature-collection`, `mid-season-sale`, `best-sellers`.

### `products`
| Column | Type / notes |
|---|---|
| `id` | PK (orderable, stable — cart references it) |
| `name_en`, `name_ar` | localized name |
| `price` | decimal(10,2) |
| `category_id` | FK → categories |
| `description_en`, `description_ar` | optional |
| `image_url` | varchar |
| `is_featured` | tinyint bool |
| `deleted_at` | **soft delete required** — the demo sells items out *and* brings them back for sales; reversible without reseeding |

Seed: the **18 products** currently in `src/data/catalog.js`.

### `product_sizes`
| Column | Type / notes |
|---|---|
| `id` | PK |
| `product_id` | FK → products |
| `size` | varchar (e.g. `'M'`) |
| `quantity` | unsigned int (0 = out of stock) |

**Unique index on (`product_id`, `size`).** A product with no rows returns an empty `sizes` array → frontend hides the size selector.

### `reviews`
`id`, `product_id` FK, `author`, `role_en`/`role_ar`, `rating` (1..5), `text_en`/`text_ar`, `avatar_url`, timestamps.

### `promo` (banner + countdown)
`title_en`/`title_ar`, `discount`, `subtitle_en`/`subtitle_ar`, `image_url`, `ends_at` (timestamp — frontend counts down live), `cta_label`/`cta_url` (or a single-row settings table).

### `testimonials`
`id`, `author`, `role_en`/`role_ar`, `rating` (1..5), `text_en`/`text_ar`, `avatar_url`, `sort_order`.

### `orders` + `order_items`
- `orders` — `id`, `user_id` FK (nullable, guests allowed), `status` (enum: pending / paid / shipped…), `total` decimal(10,2), timestamps.
- `order_items` — `id`, `order_id` FK, `product_id` FK, **`size` varchar**, `unit_price` decimal(10,2), `quantity`.
- **Order-time stock rule:** when an order is placed, re-check `product_sizes.quantity`. If a size sold out since the page loaded, reject with **`409 { "message": "Size «M» is out of stock" }`** — never oversell. Seat this as a transaction + `CHECK (quantity <= stock)` at write time.

### `carts` (optional / future guest-cart sync)
Frontend cart is localStorage-only today. If you add server-side carts later: `id`, `user_id` FK nullable, `browser_key` (guest id set by client), `product_id` FK, `size`, `quantity`, unique(`user_or_browser`, `product_id`, `size`).

---

## 6. API routes (all JSON)

### Auth — Sanctum SPA
| Method | Path | Purpose |
|---|---|---|
| GET | `/sanctum/csrf-cookie` | prime CSRF cookie (frontend calls before auth requests) |
| GET | `/api/auth/me` | current user |
| POST | `/api/auth/login` | `{ email, password }` |
| POST | `/api/auth/register` | `{ name, email, password }` |
| POST | `/api/auth/logout` | revoke session (`Accept: application/json`) |

### Catalog / content
| Method | Path | Query | Purpose |
|---|---|---:|---|
| GET | `/api/home` | `locale` | **aggregated home feed** (hero, categories, best_sellers, promo, promo_unit, testimonials) |
| GET | `/api/products` | `category`, `sort` (`recommended`\|`price-low`\|`price-high`\|`name`), `locale`, page | shop grid, paginated |
| GET | `/api/products/{id}` | `locale` | product detail |
| GET | `/api/products/{id}/related` | `limit`, `locale` | related grid |
| GET | `/api/products/{id}/stock` | — | **per-size availability** for the size selector |
| POST | `/api/products/{id}/reviews` | — | submit a review |

### Orders (to build)
| Method | Path | Purpose |
|---|---|---|
| POST | `/api/orders` | place order — **re-check stock per size → 409 on sold-out**; saves order + order_items, decrements `product_sizes.quantity` |

### Response envelope (apply to every endpoint)
Every body is wrapped: `{ "data": ... }`. List endpoints add pagination: `{ "data": [...], "meta": { "current_page", "last_page", "total" } }` (frontend default: 12 per page).

Example — `GET /api/products/{id}/stock`:
```json
{ "data": { "product_id": 1, "sizes": [
  { "size": "XS", "in_stock": true,  "quantity": 5 },
  { "size": "M",  "in_stock": false, "quantity": 0 }
] } }
```

Example — `GET /api/products?locale=ar` (names already localized):
```json
{ "data": [ { "id": 1, "name": "تیشیرت أبيض كلاسيكي", "price": 24.99, "category": "signature-collection", "image": "https://…/w=800" } ], "meta": { "current_page": 1, "last_page": 2, "total": 18 } }
```

Full `GET /api/home` payload shape: see `apis.md` §1 (hero slides, categories with `slug/title/eyebrow/count/url`, `best_sellers`, `promo`, `promo_unit`, `testimonials`).

---

## 7. Validation & error conventions

| Status | Meaning / contract |
|---|---|
| `422` | Validation failed — body `{ "message": "...", "errors": { "field": ["msg"] } }` (Login/Register views already render per-field messages) |
| `401` | Unauthenticated / session expired (frontend redirects to `/login`) |
| `419` | CSRF token mismatch (frontend retries once automatically) |
| `409` | **Stock conflict at order time** — `{ "message": "Size «M» is out of stock" }` |
| Other | Return `{ "message": "..." }` JSON — **never HTML** |

---

## 8. Seed-data requirements (so the site renders with zero frontend change)

1. Categories: slugs `signature-collection`, `mid-season-sale`, `best-sellers` exactly as above.
2. Products: the 18 catalog items from `src/data/catalog.js` (ids 1–18), `price` decimal, `category_id` mapped to the 3 slugs, Unsplash `image_url`s.
3. Sizes: XS, S, M, L, XL with varied quantities (frontend already disables sold-out sizes).
4. Promo: `ends_at` in the future (e.g. `2026-12-31T23:59:59`), localized title/subtitle/discount/CTAs.
5. Testimonials + reviews: seed a few so home sections aren't empty.
6. Share `name_ar`, `title_ar`, etc. in Arabic with the frontend `src/data/*.js` **so translations match exactly** (the Arabic strings sit in `src/data/catalog.js`, `src/data/home.js`, and `src/i18n/locales/ar.js`).

---

## 9. Backend developer checklist

- [ ] Sanctum SPA **cookie** auth (`stateful`, `Accept: application/json`), CSRF cookie on `/` route
- [ ] CORS allowed for the frontend origin; `VITE_API_URL` in the frontend `.env` pointed at the API (host `http://localhost:3200` is only the current default)
- [ ] Every endpoint returns `{ data: ... }` bodies and already-localized rows (`?locale=`, `_en` fallback)
- [ ] `product_sizes` unique index; stock endpoint `{ product_id, sizes[] }`
- [ ] **Order-time stock re-check → 409**, decrement quantities in a transaction
- [ ] Soft deletes on `products` for the sale in/out demo
- [ ] Pagination `{ data, meta }` on `/api/products`
- [ ] Seed data matching slugs + Arabic translations
- [ ] HTTPS-cookie config for production (`SameSite`, `Secure`)

> This document mirrors `apis.md` in the project root — keep both in sync as the contract evolves.