<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Validation Language Lines
  |--------------------------------------------------------------------------
  |
  | The following language lines contain the default error messages used by
  | the validator class. Some of these rules have multiple versions such
  | as the size rules. Feel free to tweak each of these messages here.
  |
  */

  'accepted' => 'يجب قبول حقل :attribute.',
  'accepted_if' => 'يجب قبول حقل :attribute عندما يكون :other هو :value.',
  'active_url' => 'يجب أن يكون حقل :attribute رابطًا صالحًا.',
  'after' => 'يجب أن يكون حقل :attribute تاريخًا بعد :date.',
  'after_or_equal' => 'يجب أن يكون حقل :attribute تاريخًا بعد :date أو مساويًا له.',
  'alpha' => 'يجب أن يحتوي حقل :attribute على حروف فقط.',
  'alpha_dash' => 'يجب أن يحتوي حقل :attribute على حروف وأرقام وشرطات وشرطات سفلية فقط.',
  'alpha_num' => 'يجب أن يحتوي حقل :attribute على حروف وأرقام فقط.',
  'array' => 'يجب أن يكون حقل :attribute مصفوفة.',
  'ascii' => 'يجب أن يحتوي حقل :attribute على أحرف أبجدية رقمية ورموز أحادية البايت فقط.',
  'before' => 'يجب أن يكون حقل :attribute تاريخًا قبل :date.',
  'before_or_equal' => 'يجب أن يكون حقل :attribute تاريخًا قبل :date أو مساويًا له.',
  'between' => [
    'array' => 'يجب أن يحتوي حقل :attribute على عدد عناصر بين :min و :max.',
    'file' => 'يجب أن يكون حجم حقل :attribute بين :min و :max كيلوبايت.',
    'numeric' => 'يجب أن تكون قيمة حقل :attribute بين :min و :max.',
    'string' => 'يجب أن يكون طول حقل :attribute بين :min و :max حرفًا.',
  ],
  'boolean' => 'يجب أن تكون قيمة حقل :attribute صحيحة أو خاطئة.',
  'can' => 'يحتوي حقل :attribute على قيمة غير مصرح بها.',
  'confirmed' => 'تأكيد حقل :attribute غير مطابق.',
  'contains' => 'يفتقد حقل :attribute قيمة مطلوبة.',
  'current_password' => 'كلمة المرور غير صحيحة.',
  'date' => 'يجب أن يكون حقل :attribute تاريخًا صالحًا.',
  'date_equals' => 'يجب أن يكون حقل :attribute تاريخًا مطابقًا لـ :date.',
  'date_format' => 'يجب أن يطابق حقل :attribute التنسيق :format.',
  'decimal' => 'يجب أن يحتوي حقل :attribute على :decimal منازل عشرية.',
  'declined' => 'يجب رفض حقل :attribute.',
  'declined_if' => 'يجب رفض حقل :attribute عندما يكون :other هو :value.',
  'different' => 'يجب أن يختلف حقل :attribute عن :other.',
  'digits' => 'يجب أن يتكون حقل :attribute من :digits أرقام.',
  'digits_between' => 'يجب أن يتكون حقل :attribute من عدد أرقام بين :min و :max.',
  'dimensions' => 'أبعاد الصورة في حقل :attribute غير صالحة.',
  'distinct' => 'يحتوي حقل :attribute على قيمة مكررة.',
  'doesnt_end_with' => 'يجب ألا ينتهي حقل :attribute بأحد التالي: :values.',
  'doesnt_start_with' => 'يجب ألا يبدأ حقل :attribute بأحد التالي: :values.',
  'email' => 'يجب أن يكون حقل :attribute بريدًا إلكترونيًا صالحًا.',
  'ends_with' => 'يجب أن ينتهي حقل :attribute بأحد التالي: :values.',
  'enum' => 'القيمة المحددة في :attribute غير صالحة.',
  'exists' => 'القيمة المحددة في :attribute غير موجودة.',
  'extensions' => 'يجب أن يحمل حقل :attribute أحد الامتدادات التالية: :values.',
  'file' => 'يجب أن يكون حقل :attribute ملفًا.',
  'filled' => 'يجب أن تحتوي حقل :attribute على قيمة.',
  'gt' => [
    'array' => 'يجب أن يحتوي حقل :attribute على أكثر من :value عناصر.',
    'file' => 'يجب أن يكون حجم حقل :attribute أكبر من :value كيلوبايت.',
    'numeric' => 'يجب أن تكون قيمة حقل :attribute أكبر من :value.',
    'string' => 'يجب أن يكون طول حقل :attribute أكبر من :value حرفًا.',
  ],
  'gte' => [
    'array' => 'يجب أن يحتوي حقل :attribute على :value عناصر أو أكثر.',
    'file' => 'يجب أن يكون حجم حقل :attribute أكبر من أو يساوي :value كيلوبايت.',
    'numeric' => 'يجب أن تكون قيمة حقل :attribute أكبر من أو تساوي :value.',
    'string' => 'يجب أن يكون طول حقل :attribute أكبر من أو يساوي :value حرفًا.',
  ],
  'hex_color' => 'يجب أن يكون حقل :attribute لونًا سداسيًا صالحًا.',
  'image' => 'يجب أن يكون حقل :attribute صورة.',
  'in' => 'القيمة المحددة في :attribute غير صالحة.',
  'in_array' => 'يجب أن توجد قيمة حقل :attribute في :other.',
  'integer' => 'يجب أن يكون حقل :attribute عددًا صحيحًا.',
  'ip' => 'يجب أن يكون حقل :attribute عنوان IP صالحًا.',
  'ipv4' => 'يجب أن يكون حقل :attribute عنوان IPv4 صالحًا.',
  'ipv6' => 'يجب أن يكون حقل :attribute عنوان IPv6 صالحًا.',
  'json' => 'يجب أن يكون حقل :attribute نص JSON صالحًا.',
  'list' => 'يجب أن يكون حقل :attribute قائمة.',
  'lowercase' => 'يجب أن يكون حقل :attribute بأحرف صغيرة.',
  'lt' => [
    'array' => 'يجب أن يحتوي حقل :attribute على أقل من :value عناصر.',
    'file' => 'يجب أن يكون حجم حقل :attribute أقل من :value كيلوبايت.',
    'numeric' => 'يجب أن تكون قيمة حقل :attribute أقل من :value.',
    'string' => 'يجب أن يكون طول حقل :attribute أقل من :value حرفًا.',
  ],
  'lte' => [
    'array' => 'يجب ألا يحتوي حقل :attribute على أكثر من :value عناصر.',
    'file' => 'يجب أن يكون حجم حقل :attribute أقل من أو يساوي :value كيلوبايت.',
    'numeric' => 'يجب أن تكون قيمة حقل :attribute أقل من أو تساوي :value.',
    'string' => 'يجب أن يكون طول حقل :attribute أقل من أو يساوي :value حرفًا.',
  ],
  'mac_address' => 'يجب أن يكون حقل :attribute عنوان MAC صالحًا.',
  'max' => [
    'array' => 'يجب ألا يحتوي حقل :attribute على أكثر من :max عناصر.',
    'file' => 'يجب ألا يزيد حجم حقل :attribute عن :max كيلوبايت.',
    'numeric' => 'يجب ألا تزيد قيمة حقل :attribute عن :max.',
    'string' => 'يجب ألا يزيد طول حقل :attribute عن :max حرفًا.',
  ],
  'max_digits' => 'يجب ألا يزيد عدد أرقام حقل :attribute عن :max.',
  'mimes' => 'يجب أن يكون حقل :attribute ملفًا من نوع: :values.',
  'mimetypes' => 'يجب أن يكون حقل :attribute ملفًا من نوع: :values.',
  'min' => [
    'array' => 'يجب أن يحتوي حقل :attribute على :min عناصر على الأقل.',
    'file' => 'يجب أن يكون حجم حقل :attribute :min كيلوبايت على الأقل.',
    'numeric' => 'يجب أن تكون قيمة حقل :attribute :min على الأقل.',
    'string' => 'يجب أن يكون طول حقل :attribute :min أحرف على الأقل.',
  ],
  'min_digits' => 'يجب أن يتكون حقل :attribute من :min أرقام على الأقل.',
  'missing' => 'يجب أن يكون حقل :attribute غائبًا.',
  'missing_if' => 'يجب أن يكون حقل :attribute غائبًا عندما يكون :other هو :value.',
  'missing_unless' => 'يجب أن يكون حقل :attribute غائبًا إلا إذا كان :other هو :value.',
  'missing_with' => 'يجب أن يكون حقل :attribute غائبًا عند وجود :values.',
  'missing_with_all' => 'يجب أن يكون حقل :attribute غائبًا عند وجود :values.',
  'multiple_of' => 'يجب أن تكون قيمة حقل :attribute من مضاعفات :value.',
  'not_in' => 'القيمة المحددة في :attribute غير صالحة.',
  'not_regex' => 'تنسيق حقل :attribute غير صالح.',
  'numeric' => 'يجب أن يكون حقل :attribute رقمًا.',
  'password' => [
    'letters' => 'يجب أن يحتوي حقل :attribute على حرف واحد على الأقل.',
    'mixed' => 'يجب أن يحتوي حقل :attribute على حرف كبير وحرف صغير على الأقل.',
    'numbers' => 'يجب أن يحتوي حقل :attribute على رقم واحد على الأقل.',
    'symbols' => 'يجب أن يحتوي حقل :attribute على رمز واحد على الأقل.',
    'uncompromised' => 'ظهرت قيمة :attribute في تسريب بيانات. يرجى اختيار :attribute مختلفة.',
  ],
  'present' => 'يجب أن يكون حقل :attribute موجودًا.',
  'present_if' => 'يجب أن يكون حقل :attribute موجودًا عندما يكون :other هو :value.',
  'present_unless' => 'يجب أن يكون حقل :attribute موجودًا إلا إذا كان :other هو :value.',
  'present_with' => 'يجب أن يكون حقل :attribute موجودًا عند وجود :values.',
  'present_with_all' => 'يجب أن يكون حقل :attribute موجودًا عند وجود :values.',
  'prohibited' => 'حقل :attribute محظور.',
  'prohibited_if' => 'حقل :attribute محظور عندما يكون :other هو :value.',
  'prohibited_if_accepted' => 'حقل :attribute محظور عندما يكون :other مقبولًا.',
  'prohibited_if_declined' => 'حقل :attribute محظور عندما يكون :other مرفوضًا.',
  'prohibited_unless' => 'حقل :attribute محظور إلا إذا كان :other ضمن :values.',
  'prohibits' => 'حقل :attribute يمنع وجود :other.',
  'regex' => 'تنسيق حقل :attribute غير صالح.',
  'required' => 'حقل :attribute مطلوب.',
  'required_array_keys' => 'يجب أن يحتوي حقل :attribute على مدخلات لـ: :values.',
  'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
  'required_if_accepted' => 'حقل :attribute مطلوب عندما يكون :other مقبولًا.',
  'required_if_declined' => 'حقل :attribute مطلوب عندما يكون :other مرفوضًا.',
  'required_unless' => 'حقل :attribute مطلوب إلا إذا كان :other ضمن :values.',
  'required_with' => 'حقل :attribute مطلوب عند وجود :values.',
  'required_with_all' => 'حقل :attribute مطلوب عند وجود :values.',
  'required_without' => 'حقل :attribute مطلوب عند عدم وجود :values.',
  'required_without_all' => 'حقل :attribute مطلوب عند عدم وجود أي من :values.',
  'same' => 'يجب أن يطابق حقل :attribute :other.',
  'size' => [
    'array' => 'يجب أن يحتوي حقل :attribute على :size عناصر.',
    'file' => 'يجب أن يكون حجم حقل :attribute :size كيلوبايت.',
    'numeric' => 'يجب أن تكون قيمة حقل :attribute :size.',
    'string' => 'يجب أن يكون طول حقل :attribute :size حرفًا.',
  ],
  'starts_with' => 'يجب أن يبدأ حقل :attribute بأحد التالي: :values.',
  'string' => 'يجب أن يكون حقل :attribute نصًا.',
  'timezone' => 'يجب أن يكون حقل :attribute منطقة زمنية صالحة.',
  'unique' => 'قيمة :attribute مستخدمة بالفعل.',
  'uploaded' => 'فشل رفع :attribute.',
  'uppercase' => 'يجب أن يكون حقل :attribute بأحرف كبيرة.',
  'url' => 'يجب أن يكون حقل :attribute رابطًا صالحًا.',
  'ulid' => 'يجب أن يكون حقل :attribute معرّف ULID صالحًا.',
  'uuid' => 'يجب أن يكون حقل :attribute معرّف UUID صالحًا.',

  /*
  |--------------------------------------------------------------------------
  | Custom Validation Language Lines
  |--------------------------------------------------------------------------
  |
  | Here you may specify custom validation messages for attributes using the
  | convention "attribute.rule" to name the lines. This makes it quick to
  | specify a specific custom language line for a given attribute rule.
  |
  | These are the messages the auth endpoints document. They live here (not in
  | each FormRequest's messages()) so the ?lang parameter selects the language
  | without every request hard-coding English.
  |
  */

  'custom' => [
    'name' => [
      'required' => 'يرجى إدخال الاسم.',
      'string' => 'يجب أن يكون الاسم نصًا.',
      'max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
    ],
    'email' => [
      'required' => 'يرجى إدخال البريد الإلكتروني.',
      'string' => 'يجب أن يكون البريد الإلكتروني نصًا.',
      'email' => 'أدخل بريدًا إلكترونيًا صالحًا، مثل sara@example.com.',
      'max' => 'يجب ألا يزيد البريد الإلكتروني عن 255 حرفًا.',
      'unique' => 'يوجد حساب مسجل بهذا البريد الإلكتروني بالفعل. يمكنك تسجيل الدخول بدلًا من ذلك.',
    ],
    'password' => [
      'required' => 'يرجى إدخال كلمة مرور.',
      'string' => 'يجب أن تكون كلمة المرور نصًا.',
      'min' => 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.',
      'confirmed' => 'كلمتا المرور غير متطابقتين.',
    ],
    'device_name' => [
      'string' => 'يجب أن يكون اسم الجهاز نصًا.',
      'max' => 'يجب ألا يزيد اسم الجهاز عن 100 حرف.',
    ],
  ],

  /*
  |--------------------------------------------------------------------------
  | Custom Validation Attributes
  |--------------------------------------------------------------------------
  |
  | The following language lines are used to swap our attribute placeholder
  | with something more reader friendly such as "E-Mail Address" instead
  | of "email". This simply helps us make our message more expressive.
  |
  */

  'attributes' => [
    'name' => 'الاسم',
    'email' => 'البريد الإلكتروني',
    'password' => 'كلمة المرور',
    'device_name' => 'اسم الجهاز',
    'status' => 'الحالة',
    'items' => 'عناصر الطلب',
    'quantity' => 'الكمية',
    'product_variant_id' => 'المقاس أو اللون',
  ],

];
