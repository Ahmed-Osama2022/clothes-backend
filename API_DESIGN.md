# API Design

Target shape of the JSON API (Laravel 11, Sanctum). The current
`routes/api.php` holds throwaway test closures — this document is the contract
we build toward.

- Base URL: `/api`
- Auth: Sanctum personal access tokens
- Roles: `admin`, `customer` (many-to-many, `role_user`)
- Language: bilingual EN/AR

---

## 1. Access layers

```
                    ┌────────────────────────────────────────┐
                    │  SPA storefront (EN/AR) + admin panel   │
                    └───────────────────┬────────────────────┘
                                        │  HTTPS  ·  /api/*
            ┌───────────────────────────┼───────────────────────────┐
            │                           │                           │
     ┌──────▼──────┐            ┌───────▼────────┐          ┌───────▼────────┐
     │  PUBLIC     │            │  CUSTOMER      │          │  ADMIN         │
     │ (no auth)   │            │ auth:sanctum   │          │ auth+role:admin│
     └─────────────┘            └────────────────┘          └────────────────┘
       catalog,                   own profile,                full CRUD,
       product detail,            own orders,                 orders/status,
       variants, image URLs       place order                 users, roles,
                                                              promo, reviews
```

## 2. Resource relationships

```
Category ──< Product ──< ProductVariant >── (size, color_en/ar, color_hex, sku, price?, qty)
                 │                │
                 │                └──< OrderItem >── Order >── User(roles)
                 └──< Image (morph: category|product|variant)

price rule:  variant.price ?? product.price
stock rule:  variant.quantity
```

---

## 3. Endpoints

### Public — catalog

| Method | Path | Notes |
|---|---|---|
| GET | `/api/categories` | list + nested products (light) |
| GET | `/api/categories/{category:slug}` | bound by slug (`HasSlug`) |
| GET | `/api/products` | filters: `?category= &search= &size= &color= &min_price= &max_price= &sort= &page=` |
| GET | `/api/products/{product:slug}` | full detail incl. `variants[]`, `images[]` |
| GET | `/api/products/{product:slug}/variants` | variant list |
| GET | `/api/variants/{sku}` | barcode / warehouse / support lookup (SKU is a *filter key*, not identity) |

### Auth — Sanctum (bearer tokens, implemented)

| Method | Path | Notes |
|---|---|---|
| POST | `/api/v1/auth/register` | `{name,email,password,password_confirmation[,device_name]}` → `201` customer (`customer` role) + token; throttled 6/min |
| POST | `/api/v1/auth/login` | `{email,password[,device_name]}` → `200` token; `401` for unknown email *and* wrong password; throttled 6/min |
| POST | `/api/v1/auth/logout` | auth; revokes only the token used for the call → `204` |
| GET | `/api/v1/auth/me` | auth; user + `roles` in the camelCase `UserResource` shape |
| GET | `/api/user` | legacy Laravel skeleton route, outside v1; raw user model, no roles |
| POST | `/api/v1/auth/forgot-password` | not registered (optional) |
| POST | `/api/v1/auth/reset-password` | not registered (optional) |

New tokens are returned once as `{ "data": { "user": {...}, "token": "id|plainText", "tokenType": "Bearer" } }`;
clients send `Authorization: Bearer <token>`.

### Customer / checkout

| Method | Path | Notes |
|---|---|---|
| POST | `/api/orders` | **auth (Sanctum)**; body `items:[{product_variant_id, quantity}]`; the order is owned by the caller (`user_id` required); runs in a transaction with a per-variant stock re-check → `409` on shortfall |
| GET | `/api/me/orders` | auth: own orders |
| GET | `/api/orders/{order}` | owner or admin |
| GET | `/api/orders/{order}/items` | owner or admin |
| POST | `/api/orders/{order}/cancel` | owner, only while `pending`/`processing` |

### Admin

| Method | Path | Notes |
|---|---|---|
| CRUD | `/api/admin/products` (+ `/{product}/variants`) | `POST/PUT/DELETE`; `PUT` for stock/price |
| CRUD | `/api/admin/categories` | |
| CRUD | `/api/admin/images` | polymorphic attach/detach |
| GET/PATCH | `/api/admin/orders` · `/api/admin/orders/{order}` | change `status` |
| CRUD | `/api/admin/users` · `/api/admin/roles` | role attach/detach |
| CRUD | `/api/admin/promo` · `/api/admin/testimonials` | future tables |
| GET/PATCH | `/api/admin/reviews` | future: moderation |

---

## 4. Cross-cutting conventions

- **Localization:** `?locale=en|ar` (fallback `Accept-Language`). Read the
  `{field}_{locale}` column and return it as `name`/`description`; `_en`
  fallback when Arabic is missing. Localized columns: `name`, `description`,
  `color_en`/`color_ar` → returned as `color`.
- **Money:** `decimal(10,2)`, returned as strings (e.g. `"249.42"`);
  `order_items.unit_price` is a snapshot of the price at purchase time.
- **Responses:** consistent envelope (`data` + `meta` for pagination);
  shape payloads with Laravel API Resources.
- **Errors:** `401` unauthenticated · `403` wrong role / not owner · `404`
  missing · `422` validation · `409` stock conflict
  (`{"message": "Size «M» is out of stock"}`).
- **Identifiers:** route binding by `slug` for `Category`/`Product`, by `id` for
  `Order`/`ProductVariant`. Clients **transact** with `product_variant_id`;
  `sku` is only for display, lookup, and integration.

---

## 5. Open decisions

1. **Cart endpoints** — the cart lives in the frontend (`localStorage`), so
   `POST /api/orders` takes the items array. Keep it that way, or add a
   server-side `/api/cart`?
2. **Admin path style** — namespaced `/api/admin/*` (as above) vs role-gated
   resource routes without the prefix.
