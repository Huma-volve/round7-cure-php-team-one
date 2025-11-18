<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        if (Faq::count() > 0) {
            return;
        }

        $faqs = [
            [
                'question_en' => 'How do I book an appointment?',
                'answer_en' => 'Search for a doctor, choose an available time, and complete payment.',
                'question_ar' => 'كيف أحجز موعداً مع الطبيب؟',
                'answer_ar' => 'يمكنك حجز موعد من خلال البحث عن الطبيب المناسب ثم اختيار الوقت المتاح وإكمال الدفع.',
                'display_order' => 1,
            ],
            [
                'question_en' => 'Can I cancel my booking?',
                'answer_en' => 'Yes, cancellations are allowed up to 24 hours before the visit with refunds per policy.',
                'question_ar' => 'هل يمكنني إلغاء الحجز؟',
                'answer_ar' => 'نعم، يمكنك إلغاء الحجز قبل 24 ساعة من موعد الزيارة واسترداد الرسوم وفق سياسة الاسترجاع.',
                'display_order' => 2,
            ],
            [
                'question_en' => 'Do you offer online consultations?',
                'answer_en' => 'We provide video and audio sessions in addition to in-clinic and home visits.',
                'question_ar' => 'هل توجد استشارات عبر الإنترنت؟',
                'answer_ar' => 'نقدم جلسات فيديو وصوت مع الأطباء بالإضافة إلى الزيارات المنزلية والعيادية.',
                'display_order' => 3,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create(array_merge($faq, [
                'question' => $faq['question_en'],
                'answer' => $faq['answer_en'],
                'is_active' => true,
            ]));
        }
    }
}

