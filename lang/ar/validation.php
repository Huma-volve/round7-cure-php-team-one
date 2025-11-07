<?php

return [
    // Booking validation
    'doctor_id.required' => 'يجب اختيار طبيب',
    'doctor_id.exists' => 'الطبيب المحدد غير موجود',
    'date_time.required' => 'يجب تحديد تاريخ ووقت الموعد',
    'date_time.date' => 'تاريخ ووقت الموعد غير صحيح',
    'date_time.after' => 'لا يمكن حجز موعد في الماضي',
    'payment_method.required' => 'يجب اختيار طريقة الدفع',
    'payment_method.in' => 'طريقة الدفع غير صحيحة',

    // Reschedule validation
    'date_time.required' => 'يجب تحديد تاريخ ووقت الموعد الجديد',
    'date_time.after' => 'لا يمكن تحديد موعد في الماضي',

    // Payment validation
    'booking_id.required' => 'يجب تحديد رقم الحجز',
    'booking_id.exists' => 'الحجز المحدد غير موجود',
    'gateway.required' => 'يجب اختيار طريقة الدفع',
    'gateway.in' => 'طريقة الدفع غير صحيحة',
    'currency.required' => 'يجب تحديد العملة',
    'currency.size' => 'رمز العملة يجب أن يكون 3 أحرف',
    'amount.required' => 'يجب تحديد المبلغ',
    'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
    'amount.min' => 'المبلغ يجب أن يكون على الأقل 0.50',
    'description.max' => 'الوصف يجب ألا يتجاوز 255 حرفاً',
    'return_url.url' => 'رابط الإرجاع غير صحيح',
    'return_url.required_if' => 'رابط الإرجاع مطلوب عند استخدام PayPal',
    'cancel_url.url' => 'رابط الإلغاء غير صحيح',
    'payment_id.required' => 'يجب تحديد رقم الدفع',
    'payment_id.string' => 'رقم الدفع يجب أن يكون نصاً',

    // Standard Laravel validation messages
    'required' => 'حقل :attribute مطلوب',
    'exists' => ':attribute المحدد غير موجود',
    'date' => ':attribute يجب أن يكون تاريخاً صحيحاً',
    'after' => ':attribute يجب أن يكون بعد :date',
    'in' => ':attribute المحدد غير صحيح',
    'numeric' => ':attribute يجب أن يكون رقماً',
    'min' => ':attribute يجب أن يكون على الأقل :min',
    'max' => ':attribute يجب ألا يتجاوز :max',
    'size' => ':attribute يجب أن يكون :size',
    'url' => ':attribute يجب أن يكون رابطاً صحيحاً',
    'string' => ':attribute يجب أن يكون نصاً',
    'required_if' => 'حقل :attribute مطلوب عندما :other يكون :value',
];

