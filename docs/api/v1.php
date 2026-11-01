<?php

/**
 * API reference content for the v1 JSON API.
 *
 * This file is the single source of truth for the documentation page rendered
 * by App\Http\Controllers\Docs\ApiDocsController at GET /docs/api.
 *
 * Rule: every endpoint (new or changed) must be documented here — summary,
 * parameters, every response status, and a copy-paste accurate example.
 * Keep examples in sync with the resources in app/Http/Resources/Api/V1.
 */
return [
  'title' => 'Clothes Shop API',
  'version' => 'v1',
  'updated_at' => '2026-11-01',
  'base_url' => '/api/v1',
  'description' => 'Read-only JSON catalog API for the bilingual (EN/AR) clothes storefront. '
    . 'Every response is JSON with a top-level "data" key: an object for a single '
    . 'resource, an array for a collection. Authentication is Sanctum bearer-token based: '
    . 'register/login return a token that protected routes expect as Authorization: Bearer. '
    . 'Catalog and content routes are public.',

  'conventions' => [
    [
      'name' => 'Localization',
      'detail' => 'Send ?lang=en or ?lang=ar. Values other than en/ar fall back to the app default locale. '
        . 'Localized text is read from "{field}_{locale}" columns with an "_en" fallback. '
        . '"name" is always the base (English) name; "title" and "description" are translated. '
        . 'Validation (422) and authentication (401) messages follow the same parameter, so ?lang=ar '
        . 'returns Arabic errors.',
    ],
    [
      'name' => 'Slugs',
      'detail' => 'Categories and products are addressed by slug, not id (HasSlug trait). Slugs are unique and '
        . 'auto-generated from the name on create when not supplied.',
    ],
    [
      'name' => 'Money',
      'detail' => 'All prices are decimal(10,2) and returned as strings (e.g. "24.99"), never floats. '
        . 'A variant with a null price falls back to the parent product price.',
    ],
    [
      'name' => 'Timestamps',
      'detail' => 'createdAt / updatedAt are ISO 8601 strings in the app timezone (Africa/Cairo).',
    ],
    [
      'name' => 'Errors',
      'detail' => 'Errors return the HTTP status plus a JSON body of { "message": string, "status": int }. '
        . 'Single-item lookups by slug respond 404 with a resource-specific message.',
    ],
    [
      'name' => 'Pagination',
      'detail' => 'Not implemented yet. List endpoints currently return the full collection inside "data" '
        . 'with no "meta" block.',
    ],
  ],

  'groups' => [
    [
      'name' => 'Catalog',
      'slug' => 'catalog',
      'description' => 'Public, unauthenticated read access to categories and products.',
      'endpoints' => [
        [
          'method' => 'GET',
          'path' => '/categories',
          'summary' => 'List all categories',
          'description' => 'Returns every category ordered by sort_order, then id. Each category includes its '
            . 'images (ordered by sort_order) and a productsCount. Products are not nested on this endpoint.',
          'auth' => 'Public',
          'parameters' => [
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language for title/description. Defaults to the application locale.',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'Category collection (truncated to one item here).',
              'example' => [
                'data' => [
                  [
                    'id' => 1,
                    'slug' => 'signature-collection',
                    'name' => 'Signature Collection',
                    'title' => 'المجموعة المميزة',
                    'description' => 'أساسيات خالدة صُنعت لتدوم.',
                    'sortOrder' => 1,
                    'productsCount' => 6,
                    'images' => [
                      ['id' => 1, 'url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&h=1200&q=80', 'sortOrder' => 1, 'createdAt' => '2026-11-01T20:30:00+02:00', 'updatedAt' => '2026-11-01T20:30:00+02:00'],
                      ['id' => 2, 'url' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=900&h=1200&q=80', 'sortOrder' => 2, 'createdAt' => '2026-11-01T20:30:00+02:00', 'updatedAt' => '2026-11-01T20:30:00+02:00'],
                    ],
                    'createdAt' => '2026-10-21T00:59:37+03:00',
                    'updatedAt' => '2026-10-21T00:59:37+03:00',
                  ],
                ],
              ],
            ],
          ],
        ],
        [
          'method' => 'GET',
          'path' => '/categories/{category}',
          'summary' => 'Show one category by slug',
          'description' => 'Returns a single category with its images, productsCount, and its products. '
            . 'Products nested here are summaries: images and variants are omitted because they are not '
            . 'eager loaded on this endpoint.',
          'auth' => 'Public',
          'parameters' => [
            [
              'name' => 'category',
              'in' => 'path',
              'type' => 'string',
              'required' => true,
              'description' => 'Category slug, e.g. signature-collection, mid-season-sale, best-sellers.',
            ],
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language for title/description. Defaults to the application locale.',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'Single category (products truncated to one item here).',
              'example' => [
                'data' => [
                  'id' => 1,
                  'slug' => 'signature-collection',
                  'name' => 'Signature Collection',
                  'title' => 'المجموعة المميزة',
                  'description' => 'أساسيات خالدة صُنعت لتدوم.',
                  'sortOrder' => 1,
                  'productsCount' => 6,
                  'images' => [
                    ['id' => 1, 'url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&h=1200&q=80', 'sortOrder' => 1, 'createdAt' => '2026-11-01T20:30:00+02:00', 'updatedAt' => '2026-11-01T20:30:00+02:00'],
                  ],
                  'products' => [
                    [
                      'id' => 1,
                      'slug' => 'classic-white-tee',
                      'name' => 'Classic White Tee',
                      'title' => 'تيشيرت أبيض كلاسيكي',
                      'description' => 'تيشيرت أبيض كلاسيكي مصنوع من قماش ناعم…',
                      'price' => '24.99',
                      'isFeatured' => true,
                      'sortOrder' => 1,
                      'categoryName' => 'Signature Collection',
                      'createdAt' => '2026-10-21T00:59:40+03:00',
                      'updatedAt' => '2026-10-21T00:59:40+03:00',
                    ],
                  ],
                  'createdAt' => '2026-10-21T00:59:37+03:00',
                  'updatedAt' => '2026-10-21T00:59:37+03:00',
                ],
              ],
            ],
            [
              'status' => 404,
              'description' => 'No category with that slug.',
              'example' => [
                'message' => 'Category (unknown-slug) not found, try to refresh the category list or use the correct slug name.',
                'status' => 404,
              ],
            ],
          ],
        ],
        [
          'method' => 'GET',
          'path' => '/products',
          'summary' => 'List all products',
          'description' => 'Returns every product ordered by sort_order, then id, with categoryName, images, and '
            . 'all variants (ordered by sort_order). No filtering, sorting or pagination is implemented yet. '
            . 'Variant order is only as stable as sort_order, which the seeder leaves at 0.',
          'auth' => 'Public',
          'parameters' => [
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language for title/description. Defaults to the application locale.',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'Product collection (truncated to one product and two variants here).',
              'example' => [
                'data' => [
                  [
                    'id' => 1,
                    'slug' => 'classic-white-tee',
                    'name' => 'Classic White Tee',
                    'title' => 'تيشيرت أبيض كلاسيكي',
                    'description' => 'تيشيرت أبيض كلاسيكي مصنوع من قماش ناعم…',
                    'price' => '24.99',
                    'isFeatured' => true,
                    'sortOrder' => 1,
                    'categoryName' => 'Signature Collection',
                    'images' => [
                      ['id' => 7, 'url' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=900&h=1200&q=80', 'sortOrder' => 1, 'createdAt' => '2026-11-01T20:30:00+02:00', 'updatedAt' => '2026-11-01T20:30:00+02:00'],
                    ],
                    'variants' => [
                      [
                        'id' => 1,
                        'size' => 'XS',
                        'color' => 'White',
                        'colorHex' => '#FFFFFF',
                        'sku' => 'CLASSIC-WHITE-TEE-XS-WHITE',
                        'price' => null,
                        'quantity' => 11,
                        'isActive' => true,
                        'sortOrder' => 0,
                      ],
                      [
                        'id' => 6,
                        'size' => 'XS',
                        'color' => 'Black',
                        'colorHex' => '#111111',
                        'sku' => 'CLASSIC-WHITE-TEE-XS-BLACK',
                        'price' => null,
                        'quantity' => 19,
                        'isActive' => true,
                        'sortOrder' => 0,
                      ],
                    ],
                    'createdAt' => '2026-10-21T00:59:40+03:00',
                    'updatedAt' => '2026-10-21T00:59:40+03:00',
                  ],
                ],
              ],
            ],
          ],
        ],
        [
          'method' => 'GET',
          'path' => '/products/{product}',
          'summary' => 'Show one product by slug',
          'description' => 'Returns a single product with its categoryName, images, and every variant. '
            . 'This is the endpoint the product page uses for the size/color selector; quantity per variant '
            . 'is the available stock (0 = sold out).',
          'auth' => 'Public',
          'parameters' => [
            [
              'name' => 'product',
              'in' => 'path',
              'type' => 'string',
              'required' => true,
              'description' => 'Product slug, e.g. classic-white-tee.',
            ],
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language for title/description. Defaults to the application locale.',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'Single product (variants truncated to two here).',
              'example' => [
                'data' => [
                  'id' => 1,
                  'slug' => 'classic-white-tee',
                  'name' => 'Classic White Tee',
                  'title' => 'Classic White Tee',
                  'description' => 'Classic White Tee is cut from soft, breathable fabric for effortless everyday wear.',
                  'price' => '24.99',
                  'isFeatured' => true,
                  'sortOrder' => 1,
                  'categoryName' => 'Signature Collection',
                  'images' => [
                    ['id' => 7, 'url' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=900&h=1200&q=80', 'sortOrder' => 1, 'createdAt' => '2026-11-01T20:30:00+02:00', 'updatedAt' => '2026-11-01T20:30:00+02:00'],
                  ],
                  'variants' => [
                    [
                      'id' => 1,
                      'size' => 'XS',
                      'color' => 'White',
                      'colorHex' => '#FFFFFF',
                      'sku' => 'CLASSIC-WHITE-TEE-XS-WHITE',
                      'price' => null,
                      'quantity' => 11,
                      'isActive' => true,
                      'sortOrder' => 0,
                    ],
                    [
                      'id' => 5,
                      'size' => 'XS',
                      'color' => 'Beige',
                      'colorHex' => '#F5F5DC',
                      'sku' => 'CLASSIC-WHITE-TEE-XS-BEIGE',
                      'price' => null,
                      'quantity' => 0,
                      'isActive' => true,
                      'sortOrder' => 0,
                    ],
                  ],
                  'createdAt' => '2026-10-21T00:59:40+03:00',
                  'updatedAt' => '2026-10-21T00:59:40+03:00',
                ],
              ],
            ],
            [
              'status' => 404,
              'description' => 'No product with that slug.',
              'example' => [
                'message' => 'Product (unknown-slug) not found, try to refresh the product list or use the correct slug name.',
                'status' => 404,
              ],
            ],
          ],
        ],
      ],
    ],
    [
      'name' => 'Orders',
      'slug' => 'orders',
      'description' => 'Checkout and order history. Every order belongs to a customer, so the whole group requires '
        . 'a Sanctum token: customers place and read their own orders, admins manage every order.',
      'endpoints' => [
        [
          'method' => 'GET',
          'path' => '/orders',
          'summary' => 'List every order (admin)',
          'description' => 'Returns all orders newest first, each with its customer, line items and an itemsCount. '
            . 'A customer only sees their own orders through GET /orders/{order}; a shop-wide list is admin-only.',
          'auth' => 'Sanctum (admin only)',
          'parameters' => [
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language for the nested product title and variant color. Defaults to the application locale.',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'All orders, newest first.',
              'example' => [
                'data' => [
                  [
                    'id' => 24,
                    'status' => 'pending',
                    'totalAmount' => '89.48',
                    'itemsCount' => 2,
                    'customer' => [
                      'id' => 11,
                      'name' => 'Test User',
                      'email' => 'test@example.com',
                    ],
                    'items' => [
                      [
                        'id' => 41,
                        'productId' => 7,
                        'productVariantId' => 21,
                        'quantity' => 2,
                        'unitPrice' => '19.99',
                        'lineTotal' => '39.98',
                        'product' => [
                          'id' => 7,
                          'slug' => 'classic-white-tee',
                          'name' => 'Classic White Tee',
                          'title' => 'Classic White Tee',
                        ],
                        'variant' => [
                          'id' => 21,
                          'size' => 'M',
                          'color' => 'White',
                          'colorHex' => '#FFFFFF',
                          'sku' => 'CLASSIC-WHITE-TEE-M-WHITE',
                        ],
                      ],
                      [
                        'id' => 42,
                        'productId' => 9,
                        'productVariantId' => 33,
                        'quantity' => 1,
                        'unitPrice' => '49.50',
                        'lineTotal' => '49.50',
                        'product' => [
                          'id' => 9,
                          'slug' => 'boutique-blazer',
                          'name' => 'Boutique Blazer',
                          'title' => 'Boutique Blazer',
                        ],
                        'variant' => [
                          'id' => 33,
                          'size' => 'L',
                          'color' => 'Navy',
                          'colorHex' => '#001F3F',
                          'sku' => 'BOUTIQUE-BLAZER-L-NAVY',
                        ],
                      ],
                    ],
                    'createdAt' => '2026-11-01T20:30:00+02:00',
                    'updatedAt' => '2026-11-01T20:30:00+02:00',
                  ],
                ],
              ],
            ],
            [
              'status' => 401,
              'description' => 'No token/session was sent.',
              'example' => [
                'message' => 'Authentication required. Please login first',
                'status' => 401,
              ],
            ],
            [
              'status' => 403,
              'description' => 'Authenticated, but not an admin.',
              'example' => ['message' => 'This action is unauthorized.'],
            ],
          ],
        ],
        [
          'method' => 'POST',
          'path' => '/orders',
          'summary' => 'Place an order (authenticated customer)',
          'description' => 'Creates the order and its lines in a single transaction for the authenticated customer. '
            . 'Prices are never accepted from the client: each line snapshots the variant price (or the parent '
            . 'product price when the variant price is null) and the total is recomputed server-side. Stock is '
            . 'decremented with a guarded update, so two simultaneous checkouts cannot oversell — the loser '
            . 'receives 409.',
          'auth' => 'Sanctum',
          'parameters' => [
            [
              'name' => 'items',
              'in' => 'body',
              'type' => 'array (min 1)',
              'required' => true,
              'description' => 'Cart lines. Request example: {"items":[{"product_variant_id":21,"quantity":2}]}',
            ],
            [
              'name' => 'items.*.product_variant_id',
              'in' => 'body',
              'type' => 'integer',
              'required' => true,
              'description' => 'Id of the size/color variant being bought. Must exist and may appear only once.',
            ],
            [
              'name' => 'items.*.quantity',
              'in' => 'body',
              'type' => 'integer (1-99)',
              'required' => true,
              'description' => 'Units requested for that variant; rejected with 409 when it exceeds available stock.',
            ],
          ],
          'responses' => [
            [
              'status' => 201,
              'description' => 'Order placed. Stock has already been decremented and the response contains the snapshots.',
              'example' => [
                'data' => [
                  'id' => 24,
                  'status' => 'pending',
                  'totalAmount' => '39.98',
                  'itemsCount' => 1,
                  'customer' => [
                    'id' => 11,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                  ],
                  'items' => [
                    [
                      'id' => 41,
                      'productId' => 7,
                      'productVariantId' => 21,
                      'quantity' => 2,
                      'unitPrice' => '19.99',
                      'lineTotal' => '39.98',
                      'product' => [
                        'id' => 7,
                        'slug' => 'classic-white-tee',
                        'name' => 'Classic White Tee',
                        'title' => 'Classic White Tee',
                      ],
                      'variant' => [
                        'id' => 21,
                        'size' => 'M',
                        'color' => 'White',
                        'colorHex' => '#FFFFFF',
                        'sku' => 'CLASSIC-WHITE-TEE-M-WHITE',
                      ],
                    ],
                  ],
                  'createdAt' => '2026-11-01T20:30:00+02:00',
                  'updatedAt' => '2026-11-01T20:30:00+02:00',
                ],
              ],
            ],
            [
              'status' => 401,
              'description' => 'No token/session was sent — checkout requires a customer.',
              'example' => [
                'message' => 'Authentication required. Please login first',
                'status' => 401,
              ],
            ],
            [
              'status' => 409,
              'description' => 'Stock conflict — the variant sold out or was deactivated since the cart was built. '
                . 'Nothing is written; the whole transaction rolls back.',
              'example' => [
                'message' => 'Size «M» is out of stock: only 0 left, 2 requested.',
                'status' => 409,
              ],
            ],
            [
              'status' => 422,
              'description' => 'Validation failed (empty items, unknown variant, duplicate variant line, bad quantity).',
              'example' => [
                'message' => 'The items field is required.',
                'errors' => [
                  'items' => ['The items field is required.'],
                ],
              ],
            ],
          ],
        ],
        [
          'method' => 'GET',
          'path' => '/orders/{order}',
          'summary' => 'Show a single order (owner or admin)',
          'description' => 'Returns one order with its lines. The owner sees their own order; admins see any order.',
          'auth' => 'Sanctum (owner or admin)',
          'parameters' => [
            [
              'name' => 'order',
              'in' => 'path',
              'type' => 'integer',
              'required' => true,
              'description' => 'Order id.',
            ],
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language for the nested product title and variant color.',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'The order.',
              'example' => [
                'data' => [
                  'id' => 24,
                  'status' => 'shipped',
                  'totalAmount' => '89.48',
                  'itemsCount' => 1,
                  'customer' => [
                    'id' => 11,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                  ],
                  'items' => [
                    [
                      'id' => 41,
                      'productId' => 7,
                      'productVariantId' => 21,
                      'quantity' => 2,
                      'unitPrice' => '19.99',
                      'lineTotal' => '39.98',
                      'product' => [
                        'id' => 7,
                        'slug' => 'classic-white-tee',
                        'name' => 'Classic White Tee',
                        'title' => 'تيشيرت أبيض كلاسيكي',
                      ],
                      'variant' => [
                        'id' => 21,
                        'size' => 'M',
                        'color' => 'أبيض',
                        'colorHex' => '#FFFFFF',
                        'sku' => 'CLASSIC-WHITE-TEE-M-WHITE',
                      ],
                    ],
                  ],
                  'createdAt' => '2026-11-01T20:30:00+02:00',
                  'updatedAt' => '2026-11-01T20:30:00+02:00',
                ],
              ],
            ],
            [
              'status' => 401,
              'description' => 'No token/session was sent.',
              'example' => [
                'message' => 'Authentication required. Please login first',
                'status' => 401,
              ],
            ],
            [
              'status' => 403,
              'description' => 'The order belongs to another customer and the caller is not an admin.',
              'example' => ['message' => 'This action is unauthorized.'],
            ],
            [
              'status' => 404,
              'description' => 'No order with that id.',
              'example' => [
                'message' => 'Order (999999) not found, try to refresh the order list or use the correct id.',
                'status' => 404,
              ],
            ],
          ],
        ],
        [
          'method' => 'PUT|PATCH',
          'path' => '/orders/{order}',
          'summary' => 'Change the order status (admin)',
          'description' => 'Moves an order along its lifecycle. Only status is mutable — items, price snapshots and '
            . 'totalAmount are historical records and cannot be rewritten. Allowed values: pending, processing, '
            . 'shipped, delivered, cancelled.',
          'auth' => 'Sanctum (admin only)',
          'parameters' => [
            [
              'name' => 'order',
              'in' => 'path',
              'type' => 'integer',
              'required' => true,
              'description' => 'Order id.',
            ],
            [
              'name' => 'status',
              'in' => 'body',
              'type' => 'string (pending | processing | shipped | delivered | cancelled)',
              'required' => true,
              'description' => 'New status. Request example: {"status":"shipped"}',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'The updated order.',
              'example' => [
                'data' => [
                  'id' => 24,
                  'status' => 'shipped',
                  'totalAmount' => '89.48',
                  'itemsCount' => 1,
                  'customer' => [
                    'id' => 11,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                  ],
                  'items' => [
                    [
                      'id' => 41,
                      'productId' => 7,
                      'productVariantId' => 21,
                      'quantity' => 2,
                      'unitPrice' => '19.99',
                      'lineTotal' => '39.98',
                      'product' => [
                        'id' => 7,
                        'slug' => 'classic-white-tee',
                        'name' => 'Classic White Tee',
                        'title' => 'Classic White Tee',
                      ],
                      'variant' => [
                        'id' => 21,
                        'size' => 'M',
                        'color' => 'White',
                        'colorHex' => '#FFFFFF',
                        'sku' => 'CLASSIC-WHITE-TEE-M-WHITE',
                      ],
                    ],
                  ],
                  'createdAt' => '2026-11-01T20:30:00+02:00',
                  'updatedAt' => '2026-11-02T09:15:00+02:00',
                ],
              ],
            ],
            [
              'status' => 401,
              'description' => 'No token/session was sent.',
              'example' => [
                'message' => 'Authentication required. Please login first',
                'status' => 401,
              ],
            ],
            [
              'status' => 403,
              'description' => 'Authenticated, but not an admin.',
              'example' => ['message' => 'This action is unauthorized.'],
            ],
            [
              'status' => 404,
              'description' => 'No order with that id.',
              'example' => [
                'message' => 'Order (999999) not found, try to refresh the order list or use the correct id.',
                'status' => 404,
              ],
            ],
            [
              'status' => 422,
              'description' => 'Status missing or outside the allowed set.',
              'example' => [
                'message' => 'The selected status is invalid.',
                'errors' => [
                  'status' => ['The selected status is invalid.'],
                ],
              ],
            ],
          ],
        ],
        [
          'method' => 'DELETE',
          'path' => '/orders/{order}',
          'summary' => 'Delete an order (admin)',
          'description' => 'Permanently removes the order; its order_items rows cascade at the database level.',
          'auth' => 'Sanctum (admin only)',
          'parameters' => [
            [
              'name' => 'order',
              'in' => 'path',
              'type' => 'integer',
              'required' => true,
              'description' => 'Order id.',
            ],
          ],
          'responses' => [
            [
              'status' => 204,
              'description' => 'Deleted. No response body.',
              'example' => null,
            ],
            [
              'status' => 401,
              'description' => 'No token/session was sent.',
              'example' => [
                'message' => 'Authentication required. Please login first',
                'status' => 401,
              ],
            ],
            [
              'status' => 403,
              'description' => 'Authenticated, but not an admin.',
              'example' => ['message' => 'This action is unauthorized.'],
            ],
            [
              'status' => 404,
              'description' => 'No order with that id.',
              'example' => [
                'message' => 'Order (999999) not found, try to refresh the order list or use the correct id.',
                'status' => 404,
              ],
            ],
          ],
        ],
      ],
    ],
    [
      'name' => 'Content',
      'slug' => 'content',
      'description' => 'Editorial pages shared by every storefront client (Vue SPA, Flutter app). '
        . 'Content is pre-translated and returned as structured blocks, never HTML.',
      'endpoints' => [
        [
          'method' => 'GET',
          'path' => '/about',
          'summary' => 'About Us page',
          'description' => 'Returns the published "about" page as one aggregate, cacheable payload. '
            . 'Blocks are grouped by kind so clients render them natively: "sections" (heading + body), '
            . '"stats" (label + value), "contact" (label + value) and "social" (platform + url). '
            . 'Responses carry an ETag and Cache-Control: public, max-age=300 — send If-None-Match to get a 304.',
          'auth' => 'Public',
          'parameters' => [
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language for title, intro and every block heading/body. '
                . 'Values other than en/ar fall back to the application locale.',
            ],
            [
              'name' => 'If-None-Match',
              'in' => 'header',
              'type' => 'string',
              'required' => false,
              'description' => 'ETag from a previous response; answering 304 means the cached copy is still valid.',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'The localized about page. Always includes every array, empty when unused.',
              'example' => [
                'data' => [
                  'slug' => 'about',
                  'title' => 'About Us',
                  'intro' => 'A bilingual clothing store built around timeless essentials, honest prices and pieces you will actually wear.',
                  'sections' => [
                    [
                      'heading' => 'Our Story',
                      'body' => 'We started in 2021 with a small rail of essentials and one idea: clothes that feel good, fit well, and last longer than a season.',
                    ],
                    [
                      'heading' => 'Our Mission',
                      'body' => 'Make everyday dressing simple. We keep the catalog tight, the fabrics soft, and the prices honest.',
                    ],
                  ],
                  'stats' => [
                    ['label' => 'Happy customers', 'value' => '12k+'],
                    ['label' => 'Curated products', 'value' => '500+'],
                  ],
                  'contact' => [
                    ['label' => 'Email', 'value' => 'hello@clothes-shop.test'],
                    ['label' => 'Phone', 'value' => '+20 100 000 0000'],
                  ],
                  'social' => [
                    ['platform' => 'Instagram', 'url' => 'https://instagram.com/clothes-shop'],
                  ],
                  'images' => [
                    ['id' => 61, 'url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&h=1200&q=80', 'sortOrder' => 1, 'createdAt' => '2026-11-01T20:30:00+02:00', 'updatedAt' => '2026-11-01T20:30:00+02:00'],
                  ],
                  'updatedAt' => '2026-11-01T20:30:00+02:00',
                ],
              ],
            ],
            [
              'status' => 304,
              'description' => 'The If-None-Match ETag matches; reuse the cached body.',
              'example' => null,
            ],
            [
              'status' => 404,
              'description' => 'The about page is missing or unpublished.',
              'example' => [
                'message' => 'About page not found, try again later or contact support.',
                'status' => 404,
              ],
            ],
          ],
        ],
      ],
    ],
    [
      'name' => 'Authentication',
      'slug' => 'authentication',
      'base' => '/api/v1',
      'description' => 'Stateless Sanctum bearer-token auth. POST /auth/register and POST /auth/login return a '
        . 'personal access token; send it as "Authorization: Bearer <token>" on every protected route. '
        . 'POST /auth/logout revokes the token it was called with, so signing out of one device leaves the '
        . 'others signed in. ?lang=en|ar selects the language of the user and error messages. '
        . 'A missing or invalid token answers 401 with '
        . '{ "message": "Authentication required. Please login first", "status": 401 }.',
      'endpoints' => [
        [
          'method' => 'POST',
          'path' => '/auth/register',
          'summary' => 'Register a customer and get a token',
          'description' => 'Creates the account, assigns the "customer" role (admins are never created here) and '
            . 'signs the caller in by returning a token. The email must be unused; the password is hashed and '
            . 'never returned. The plain-text token is shown once — store it, it cannot be retrieved again.',
          'auth' => 'Public (throttled to 6 requests/minute per IP)',
          'parameters' => [
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language of the response, including validation (422) and auth (401) messages. '
                . 'Defaults to the application locale.',
            ],
            [
              'name' => 'name',
              'in' => 'body',
              'type' => 'string',
              'required' => true,
              'description' => 'Customer display name, max 255 characters.',
            ],
            [
              'name' => 'email',
              'in' => 'body',
              'type' => 'string',
              'required' => true,
              'description' => 'Unique login email. Stored lowercase, so it is case-insensitive.',
            ],
            [
              'name' => 'password',
              'in' => 'body',
              'type' => 'string',
              'required' => true,
              'description' => 'At least 8 characters (Laravel password defaults). Must match password_confirmation.',
            ],
            [
              'name' => 'password_confirmation',
              'in' => 'body',
              'type' => 'string',
              'required' => true,
              'description' => 'The same password again. A missing or different value fails with 422 before the '
                . 'account is created.',
            ],
            [
              'name' => 'device_name',
              'in' => 'body',
              'type' => 'string',
              'required' => false,
              'description' => 'Label for the issued token, e.g. "iPhone 15". Defaults to "api".',
            ],
          ],
          'responses' => [
            [
              'status' => 201,
              'description' => 'Account created, token issued.',
              'example' => [
                'data' => [
                  'user' => [
                    'id' => 13,
                    'name' => 'Sara Hassan',
                    'email' => 'sara@example.com',
                    'roles' => ['customer'],
                    'emailVerifiedAt' => null,
                    'createdAt' => '2026-11-01T21:10:00+02:00',
                    'updatedAt' => '2026-11-01T21:10:00+02:00',
                  ],
                  'token' => '13|1a2b3c4d5e6f7g8h9i0jklmnopqrstuv',
                  'tokenType' => 'Bearer',
                ],
              ],
            ],
            [
              'status' => 422,
              'description' => 'Validation failed (missing fields, malformed email, email already taken, weak password, '
                . 'password_confirmation missing or different).',
              'example' => [
                'message' => 'An account with this email already exists. Try logging in instead.',
                'errors' => [
                  'email' => ['An account with this email already exists. Try logging in instead.'],
                ],
              ],
            ],
            [
              'status' => 429,
              'description' => 'Too many attempts from this IP. Retry after the Retry-After header.',
              'example' => [
                'message' => 'Too Many Attempts.',
              ],
            ],
          ],
        ],
        [
          'method' => 'POST',
          'path' => '/auth/login',
          'summary' => 'Log in and get a token',
          'description' => 'Exchanges email + password for a personal access token. Unknown email and wrong '
            . 'password both answer the same 401, so the endpoint cannot be used to discover which emails exist. '
            . 'Every login creates a new token; earlier tokens stay valid until they expire or are revoked.',
          'auth' => 'Public (throttled to 6 requests/minute per IP)',
          'parameters' => [
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language of the response, including validation (422) and auth (401) messages. '
                . 'Defaults to the application locale.',
            ],
            [
              'name' => 'email',
              'in' => 'body',
              'type' => 'string',
              'required' => true,
              'description' => 'The account email.',
            ],
            [
              'name' => 'password',
              'in' => 'body',
              'type' => 'string',
              'required' => true,
              'description' => 'The account password.',
            ],
            [
              'name' => 'device_name',
              'in' => 'body',
              'type' => 'string',
              'required' => false,
              'description' => 'Label for the issued token, e.g. "Android". Defaults to "api".',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'Credentials accepted, token issued.',
              'example' => [
                'data' => [
                  'user' => [
                    'id' => 13,
                    'name' => 'Sara Hassan',
                    'email' => 'sara@example.com',
                    'roles' => ['customer'],
                    'emailVerifiedAt' => null,
                    'createdAt' => '2026-11-01T21:10:00+02:00',
                    'updatedAt' => '2026-11-01T21:10:00+02:00',
                  ],
                  'token' => '14|z9y8x7w6v5u4t3s2r1q0ponmlkjihgfe',
                  'tokenType' => 'Bearer',
                ],
              ],
            ],
            [
              'status' => 401,
              'description' => 'Wrong email or password (deliberately indistinguishable).',
              'example' => [
                'message' => 'Invalid email or password.',
                'status' => 401,
              ],
            ],
            [
              'status' => 422,
              'description' => 'Missing or malformed fields.',
              'example' => [
                'message' => 'Please enter your email address.',
                'errors' => [
                  'email' => ['Please enter your email address.'],
                ],
              ],
            ],
            [
              'status' => 429,
              'description' => 'Too many attempts from this IP. Retry after the Retry-After header.',
              'example' => [
                'message' => 'Too Many Attempts.',
              ],
            ],
          ],
        ],
        [
          'method' => 'POST',
          'path' => '/auth/logout',
          'summary' => 'Revoke the current token',
          'description' => 'Deletes the personal access token used for the request. Other devices keep their '
            . 'tokens. The response has no body; the client should drop the token it stored.',
          'auth' => 'Sanctum',
          'parameters' => [
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language of the response, including validation (422) and auth (401) messages. '
                . 'Defaults to the application locale.',
            ],
          ],
          'responses' => [
            [
              'status' => 204,
              'description' => 'Token revoked. No response body.',
              'example' => null,
            ],
            [
              'status' => 401,
              'description' => 'No token/session was sent.',
              'example' => [
                'message' => 'Authentication required. Please login first',
                'status' => 401,
              ],
            ],
          ],
        ],
        [
          'method' => 'GET',
          'path' => '/auth/me',
          'summary' => 'Current authenticated user',
          'description' => 'Returns the caller with their role names, so a frontend can decide whether to show '
            . 'admin screens. Same user shape as the register/login payload.',
          'auth' => 'Sanctum',
          'parameters' => [
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language of the response, including validation (422) and auth (401) messages. '
                . 'Defaults to the application locale.',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'The authenticated user.',
              'example' => [
                'data' => [
                  'id' => 13,
                  'name' => 'Sara Hassan',
                  'email' => 'sara@example.com',
                  'roles' => ['customer'],
                  'emailVerifiedAt' => null,
                  'createdAt' => '2026-11-01T21:10:00+02:00',
                  'updatedAt' => '2026-11-01T21:10:00+02:00',
                ],
              ],
            ],
            [
              'status' => 401,
              'description' => 'No token/session was sent.',
              'example' => [
                'message' => 'Authentication required. Please login first',
                'status' => 401,
              ],
            ],
          ],
        ],
        [
          'method' => 'GET',
          'path' => '/user',
          // Legacy skeleton route outside the /v1 prefix.
          'base' => '/api',
          'summary' => 'Current authenticated user (legacy)',
          'description' => 'The original Laravel skeleton endpoint, kept for clients that already call it. '
            . 'Returns the raw user model (snake_case timestamps, no roles) and has no version prefix — prefer '
            . 'GET /api/v1/auth/me, which returns the documented UserResource shape.',
          'auth' => 'Sanctum',
          'parameters' => [
            [
              'name' => 'lang',
              'in' => 'query',
              'type' => 'string (en | ar)',
              'required' => false,
              'description' => 'Language of the response, including validation (422) and auth (401) messages. '
                . 'Defaults to the application locale.',
            ],
          ],
          'responses' => [
            [
              'status' => 200,
              'description' => 'The authenticated user.',
              'example' => [
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'email_verified_at' => null,
                'created_at' => '2026-10-21T00:59:36.000000Z',
                'updated_at' => '2026-10-21T00:59:36.000000Z',
              ],
            ],
            [
              'status' => 401,
              'description' => 'No token/session was sent.',
              'example' => [
                'message' => 'Authentication required. Please login first',
                'status' => 401,
              ],
            ],
          ],
        ],
      ],
    ],
  ],

  /*
  |--------------------------------------------------------------------------
  | Planned endpoints (documented contract, not implemented)
  |--------------------------------------------------------------------------
  | These routes exist only as empty controller stubs today (they answer 200
  | with an empty body). Listed here so the contract stays visible; move each
  | entry into "groups" once it is actually implemented.
  */
  'planned' => [
    ['method' => 'POST', 'path' => '/categories', 'summary' => 'Create a category', 'note' => 'Admin only. Stub.'],
    ['method' => 'PUT', 'path' => '/categories/{category}', 'summary' => 'Update a category', 'note' => 'Admin only. Stub.'],
    ['method' => 'DELETE', 'path' => '/categories/{category}', 'summary' => 'Delete a category', 'note' => 'Admin only. Stub.'],
    ['method' => 'POST', 'path' => '/products', 'summary' => 'Create a product', 'note' => 'Admin only. Stub.'],
    ['method' => 'PUT', 'path' => '/products/{product}', 'summary' => 'Update a product', 'note' => 'Admin only. Stub.'],
    ['method' => 'DELETE', 'path' => '/products/{product}', 'summary' => 'Delete a product', 'note' => 'Admin only. Stub.'],
    ['method' => 'POST', 'path' => '/orders/{order}/cancel', 'summary' => 'Cancel an order (owner, pending/processing only)', 'note' => 'Not registered.'],
    ['method' => 'GET', 'path' => '/me/orders', 'summary' => 'List the authenticated customer\'s own orders', 'note' => 'Not registered.'],
  ],
];
