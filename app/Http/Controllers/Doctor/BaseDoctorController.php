<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;

abstract class BaseDoctorController extends Controller
{
    protected function currentDoctor(): Doctor
    {
        // التأكد من استخدام web guard فقط
        $user = auth()->guard('web')->user();

        if (!$user) {
            abort(401, 'يجب تسجيل الدخول أولاً');
        }

        // التحقق من الـ role مع الـ guard
        if (!$user->hasRole('doctor', 'web')) {
            abort(403, 'ليس لديك صلاحيات كطبيب');
        }

        $doctor = $user->doctor;

        if (!$doctor) {
            abort(403, 'لم يتم العثور على ملف الطبيب. يرجى التواصل مع الإدارة.');
        }

        return $doctor;
    }
}

