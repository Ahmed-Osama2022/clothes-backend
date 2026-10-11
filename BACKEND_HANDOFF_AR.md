# تسليم الواجهة الخلفية — واجهة متجر الملابس

**التاريخ:** الأحد، 11 أكتوبر 2026
**مُعد لـ:** مطوّر الواجهة الخلفية (Laravel)
**تقنية الواجهة الأمامية:** Vue 3 + Vite + Tailwind CSS v3 SPA · Pinia · vue-router · vue-i18n · axios (جاهزة لـ Sanctum)
**ملف مرجعي:** `apis.md` (في جذر المشروع نفسه — هو عقد الاتصال المعتمد)

---

## 1. نظرة عامة على المشروع

متجر **ملابس إلكتروني ثنائي اللغة** (الإنجليزية / العربية، مع دعم الاتجاه من اليمين لليسار RTL). المشروع حاليًا **واجهة أمامية تعمل بالكامل ببيانات تجريبية (placeholder)** — الواجهة الخلفية غير موجودة بعد. جميع البيانات البعيدة مقلّدة في `src/data/*.js`، ويوجد نظامان مقلّدان (`src/api/home.js` و `src/api/stock.js`) يعيدان بالفعل **نفس هياكل JSON بالضبط التي سيرسلها API الخاص بـ Laravel**، بحيث يمكن تركيب الواجهة الخلفية دون أي تعديل على الواجهة الأمامية.

### مسارات الواجهة الأمامية (الصفحات)
| المسار | الصفحة |
|---|---|
| `/` | الرئيسية — سلايدر البطل، التصنيفات المميزة، الأكثر مبيعًا، عدّاد العرض، آراء العملاء |
| `/shop` | المتجر الكامل / شبكة المنتجات (فلتر + ترتيب + جاهزة للترقيم) |
| `/category/:slug` | صفحة التصنيف (`signature-collection`، `mid-season-sale`، `best-sellers`) |
| `/product/:id` | صفحة المنتج — **محدد المقاس + التحقق من التوفر لكل مقاس**، الكمية، الإضافة للسلة، منتجات مشابهة |
| `/login` · `/register` | صفحات تسجيل الدخول والتسجيل (تحقق فوري، جاهزة لـ Sanctum) |
| `/about` | صفحة "من نحن" ثابتة |
| `/testcard` | صفحة مكونات للتجربة/التطوير |
| `/*` | صفحة 404 |

---

## 2. ما تم إنجازه حتى الآن (الوضع الحالي)

1. **الترجمة (EN/AR + RTL)** — vue-i18n لنصوص الواجهة؛ **محتوى بيانات الطبقة** يكون مقسمًا حسب اللغة (`{ en, ar }` يُحل في الواجهة، وهو بالضبط ما سترسله الواجهة الخلفية كصفوف مترجمة). يتم تبديل `<html lang/dir>` إلى `rtl` للعربية في الواجهة نفسها — لا حاجة لأي عمل في الواجهة الخلفية من أجل RTL.
2. **محدد المقاس في صفحة المنتج** — أزرار XS/S/M/L/XL تغذّيها `GET /api/products/{id}/stock`. المقاسات غير المتوفرة تُعرض معطّلة/مشطوبة؛ اختيار مقاس صالح إلزامي قبل الإضافة للسلة؛ ويُحفظ المقاس على **سطر السلة** (أسطر السلة فريدة حسب `product_id + size`).
3. **سلة التسوق** — مخزن Pinia محفوظ في `localStorage` (`cart_key`)، مع لوحة سلة في مكون Navbar بأسطر لكل مقاس، وتحكم بالكمية، والإزالة، والإجمالي.
4. **نماذج المصادقة (جاهزة لـ Sanctum)** — `src/api/auth.js` + `src/stores/auth.js`، axios بوضع `withCredentials` (كوكيز SPA) مع خيار Bearer احتياطي. **لم تُربط بعد بأي API حقيقي.**
5. **نظام التحميل** — هياكل عظمية (shimmer) بتقنية Tailwind لكل مساحات البيانات غير المتزامنة + شريط تحميل NProgress. الواجهة تتوقع استبدال المقلّدات بعمليات حقيقية فقط بتغيير استيرادات `src/api/*`.
6. **الإشعارات (Toasts)** — مخزن إشعارات عام (`src/stores/toast.js`).

---

## 3. كيف تستهلك الواجهة الأمامية البيانات اليوم

| البيانات | المصدر المحلي (اليوم) | ستأتي من |
|---|---|---|
| التصنيفات + قائمة التنقل | `src/data/catalog.js` ← مقلّد `src/api/home.js` | `GET /api/home?locale=` |
| المنتجات (المتجر/التصنيف/المنتج/المشابهة) | `src/data/catalog.js` | `GET /api/products`، `/api/products/{id}`، `/related` |
| التوفر لكل مقاس | مقلّد `src/api/stock.js` | `GET /api/products/{id}/stock` |
| العرض + آراء العملاء | `src/data/home.js` | `GET /api/home?locale=` |
| مستخدم المصادقة | `src/api/auth.js` (غير مستخدم) | نقاط Sanctum |

### سلوك عميل axios الذي يجب أن تلتزم به الواجهة الخلفية (`src/api/client.js`)
- عنوان القاعدة من `VITE_API_URL` (يتراجع إلى `http://localhost:3200` — راجع `.env` في المشروع).
- `withCredentials: true` — يتوقع مصادقة Sanctum عبر **كوكيز httpOnly**؛ ويُرسل رأس `X-XSRF-TOKEN` يدويًا من كوكي `XSRF-TOKEN` في كل طلب.
- معالج الاستجابة يُفكّك `response.data`؛ وتُحوَّل الأخطاء إلى `Error(message)`. لذلك تقرأ الواجهة `{ data: ... }` من **JSON body** — أي أن استدعاءً حقيقيًا مثل `api.get('/products/1/stock')` يعود بجسم `{ data: { ... } }` وتُفكّك المكونات مستوى إضافيًا عبر `.data`.
- **`401`** ← يُمسح الجلسة ويُعاد التوجيه إلى `/login`.
- **`419`** (عدم تطابق CSRF) ← يُعيد ضرب `/sanctum/csrf-cookie` ويُعيد المحاولة مرة واحدة تلقائيًا.

---

## 4. عقد الترجمة (أهم قاعدة)

- الواجهة الخلفية ترسل نصوصًا **مترجمة بالفعل**. لا تطبّق الواجهة الأمامية أي ترجمة على محتوى البيانات.
- كل نقطة نهاية بيانات تقبل `?locale=en|ar` (الافتراضي `en`، والاحتياطي `en`).
- أعمدة قاعدة البيانات تتبع نمط `{field}_{locale}`: مثلًا `name_en`/`name_ar`، `title_en`/`title_ar`، `description_ar`/`description_en`… يختار Laravel العمود حسب `locale` المطلوب، مع الاحتياط إلى `_en`.
- **الحقول المستقلة عن اللغة** خارج زوج اللغة: `price`, `slug`, `id`, `image`, `size`, `count`, `rating`, `ends_at`, `avatar`.
- المقاسات `XS/S/M/L/XL` — مستقلة عن اللغة، تُرسل مرة واحدة (بدون معامل `locale` على نقطة التوفر).

---

## 5. النماذج (جداول الهجرة في Laravel)

### `users` (Sanctum)
`id`, `name`, `email` (فريد), `password`, `remember_token`, الطوابع الزمنية.

### `categories`
| العمود | النوع / ملاحظات |
|---|---|
| `id` | PK |
| `slug` | فريد — **يجب أن يطابق أسماء slugs في الواجهة تمامًا** |
| `title_en`, `title_ar` | عنوان مترجم |
| `eyebrow_en`, `eyebrow_ar` | سطر تشويقي صغير |
| `description_en`, `description_ar` | اختياري |
| `image_url` | صورة التصنيف |
| `sort_order` | int |

Slugs المطلوبة (ازرعها بالضبط، لأن روابط الواجهة تعتمد عليها):
`signature-collection`, `mid-season-sale`, `best-sellers`.

### `products`
| العمود | النوع / ملاحظات |
|---|---|
| `id` | PK (مرتب وثابت — السلة تعتمد عليه) |
| `name_en`, `name_ar` | اسم مترجم |
| `price` | decimal(10,2) |
| `category_id` | FK → categories |
| `description_en`, `description_ar` | اختياري |
| `image_url` | varchar |
| `is_featured` | tinyint bool |
| `deleted_at` | **الحذف الناعم مطلوب** — العرض يبيع المنتجات *ثم يعيدها* للعروض؛ يُرجعها دون إعادة زراعة البيانات |

الزراعة: **المنتجات الـ18** الموجودة حاليًا في `src/data/catalog.js`.

### `product_sizes`
| العمود | النوع / ملاحظات |
|---|---|
| `id` | PK |
| `product_id` | FK → products |
| `size` | varchar (مثلًا `'M'`) |
| `quantity` | unsigned int (0 = غير متوفر) |

**فهرس فريد على (`product_id`, `size`).** المنتج الذي لا يحتوي صفوفًا يعيد مصفوفة `sizes` فارغة ← تخفي الواجهة محدّد المقاس.

### `reviews`
`id`, `product_id` FK, `author`, `role_en`/`role_ar`, `rating` (1..5), `text_en`/`text_ar`, `avatar_url`, طوابع زمنية.

### `promo` (لافتة + عدّاد)
`title_en`/`title_ar`, `discount`, `subtitle_en`/`subtitle_ar`, `image_url`, `ends_at` (طابع زمني — تحسب الواجهة العدّاد مباشرة), `cta_label`/`cta_url` (أو جدول إعدادات بصف واحد).

### `testimonials`
`id`, `author`, `role_en`/`role_ar`, `rating` (1..5), `text_en`/`text_ar`, `avatar_url`, `sort_order`.

### `orders` + `order_items`
- `orders` — `id`, `user_id` FK (قابل للصفر، الضيوف مسموحون), `status` (enum: pending/paid/shipped…), `total` decimal(10,2), طوابع زمنية.
- `order_items` — `id`, `order_id` FK, `product_id` FK, **`size` varchar**, `unit_price` decimal(10,2), `quantity`.
- **قاعدة التوفر عند تقديم الطلب:** عند إنشاء طلب، أعد التحقق من `product_sizes.quantity`. إذا نفد مقاس ما منذ تحميل الصفحة، ارفض الطلب بـ **`409 { "message": "Size «M» is out of stock" }`** — لا تبِع أبدًا أكثر من المتوفر. نفّذ ذلك داخل معاملة (transaction) مع `CHECK (quantity <= stock)` عند الكتابة.

### `carts` (اختياري / لمزامنة سلة الضيوف مستقبلًا)
سلة الواجهة اليوم في `localStorage` فقط. إذا أضفت سلال خادم لاحقًا: `id`, `user_id` FK قابل للصفر, `browser_key` (مفتاح زائر يرسله العميل), `product_id` FK, `size`, `quantity`, فريد(`user_or_browser`, `product_id`, `size`).

---

## 6. مسارات API (كلها JSON)

### المصادقة — Sanctum SPA
| الطريقة | المسار | الغرض |
|---|---|---|
| GET | `/sanctum/csrf-cookie` | تهيئة كوكي CSRF (تستدعيه الواجهة قبل طلبات المصادقة) |
| GET | `/api/auth/me` | المستخدم الحالي |
| POST | `/api/auth/login` | `{ email, password }` |
| POST | `/api/auth/register` | `{ name, email, password }` |
| POST | `/api/auth/logout` | إنهاء الجلسة (`Accept: application/json`) |

### الكتالوج / المحتوى
| الطريقة | المسار | الاستعلام | الغرض |
|---|---|---:|---|
| GET | `/api/home` | `locale` | **موجز الرئيسية المُجمّع** (hero، التصنيفات، الأكثر مبيعًا، العرض، حد العرض، آراء العملاء) |
| GET | `/api/products` | `category`, `sort` (`recommended`\|`price-low`\|`price-high`\|`name`), `locale`, page | شبكة المتجر، مع ترقيم |
| GET | `/api/products/{id}` | `locale` | تفاصيل المنتج |
| GET | `/api/products/{id}/related` | `limit`, `locale` | المنتجات المشابهة |
| GET | `/api/products/{id}/stock` | — | **التوفر لكل مقاس** لمحدّد المقاس |
| POST | `/api/products/{id}/reviews` | — | إرسال تقييم |

### الطلبات (يحتاج إنشاءً)
| الطريقة | المسار | الغرض |
|---|---|---|
| POST | `/api/orders` | إنشاء طلب — **أعد التحقق من التوفر لكل مقاس ← 409 عند النفاد**؛ حفظ الطلب وأسطر `order_items`، وإنقاص `product_sizes.quantity` |

### غلاف الاستجابة (طبّقه على كل نقطة)
كل جسم مغلّف: `{ "data": ... }`. نقاط القوائم تضيف ترقيمًا: `{ "data": [...], "meta": { "current_page", "last_page", "total" } }` (الافتراضي في الواجهة: 12 لكل صفحة).

مثال — `GET /api/products/{id}/stock`:
```json
{ "data": { "product_id": 1, "sizes": [
  { "size": "XS", "in_stock": true,  "quantity": 5 },
  { "size": "M",  "in_stock": false, "quantity": 0 }
] } }
```

مثال — `GET /api/products?locale=ar` (الأسماء مترجمة بالفعل):
```json
{ "data": [ { "id": 1, "name": "تیشیرت أبيض كلاسيكي", "price": 24.99, "category": "signature-collection", "image": "https://…/w=800" } ], "meta": { "current_page": 1, "last_page": 2, "total": 18 } }
```

شكل `GET /api/home` الكامل: راجع `apis.md` القسم 1 (شرائح hero، التصنيفات مع `slug/title/eyebrow/count/url`, `best_sellers`, `promo`, `promo_unit`, `testimonials`).

---

## 7. قواعد التحقق والأخطاء

| الحالة | المعنى / العقد |
|---|---|
| `422` | فشل التحقق — الجسم `{ "message": "...", "errors": { "field": ["msg"] } }` (صفحتا الدخول والتسجيل تعرضان رسائل كل حقل بالفعل) |
| `401` | غير مصادق / انتهت الجلسة (تعيد الواجهة التوجيه إلى `/login`) |
| `419` | عدم تطابق CSRF (تعيد الواجهة المحاولة مرة واحدة تلقائيًا) |
| `409` | **تضارب التوفر عند الطلب** — `{ "message": "Size «M» is out of stock" }` |
| غيرها | أعد `{ "message": "..." }` JSON — **أبدًا ليس HTML** |

---

## 8. متطلبات بيانات الزراعة (ليعرض الموقع دون أي تعديل في الواجهة)

1. التصنيفات: slugs `signature-collection`، `mid-season-sale`، `best-sellers` تمامًا كما سبق.
2. المنتجات: أصناف الكتالوج الـ18 من `src/data/catalog.js` (المعرفات 1–18)، `price` بالعشرية، `category_id` مربوط بالـ slugs الثلاثة، `image_url`s من Unsplash.
3. المقاسات: XS, S, M, L, XL بكميات متنوعة (الواجهة تعطّل المقاسات النافدة بالفعل).
4. العرض: `ends_at` في المستقبل (مثلًا `2026-12-31T23:59:59`)، مع عنوان/سطر خصم/وصف/أزرار مترجمة.
5. آراء العملاء + التقييمات: ازرع بعضها حتى لا تخلو أقسام الرئيسية.
6. شارك النصوص العربية (`name_ar`, `title_ar`, …) مع `src/data/*.js` في الواجهة **ليتطابق الترجمة تمامًا** (النصوص العربية موجودة في `src/data/catalog.js` و`src/data/home.js` و`src/i18n/locales/ar.js`).

---

## 9. قائمة مهام مطوّر الواجهة الخلفية

- [ ] مصادقة Sanctum SPA عبر **كوكيز** (`stateful`, `Accept: application/json`)، وكوكي CSRF على مسار `/`
- [ ] CORS مسموح لأصل الواجهة الأمامية؛ وضبط `VITE_API_URL` في `.env` الخاص بالواجهة ليشير إلى الـ API (المضيف `http://localhost:3200` هو الافتراضي الحالي فقط)
- [ ] كل نقطة نهاية تعيد جسم `{ data: ... }` وصفوفًا مترجمة بالفعل (`?locale=`, مع احتياط `_en`)
- [ ] فهرس فريد في `product_sizes`؛ نقطة التوفر `{ product_id, sizes[] }`
- [ ] **التحقق من التوفر عند الطلب ← 409**، وإنقاص الكميات داخل معاملة (transaction)
- [ ] حذف ناعم في `products` لعرض إعادة البيع
- [ ] ترقيم `{ data, meta }` في `/api/products`
- [ ] زراعة بيانات مطابقة للـ slugs والترجمات العربية
- [ ] إعدادات كوكيز HTTPS للإنتاج (`SameSite`, `Secure`)

> هذه الوثيقة تعكس `apis.md` في جذر المشروع — أبقِ الملفين متزامنين مع تطور العقد.