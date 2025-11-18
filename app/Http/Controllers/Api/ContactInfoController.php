<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class ContactInfoController extends Controller
{
    use ApiResponseTrait;

    public function show(): JsonResponse
    {
        $logoPath = Setting::getValue('logo');

        $data = [
            'brand' => [
                'name' => Setting::getValue('app_name', config('app.name')),
                'tagline' => Setting::getValue('footer_tagline', Setting::getValue('description')),
                'logo' => $logoPath ? asset('storage/' . $logoPath) : null,
            ],
            'contact' => [
                'phone' => Setting::getValue('footer_phone', Setting::getValue('phone')),
                'email' => Setting::getValue('footer_email', Setting::getValue('email')),
                'address' => Setting::getValue('footer_address', Setting::getValue('address')),
            ],
            'socials' => array_filter([
                'facebook' => Setting::getValue('social_facebook'),
                'whatsapp' => Setting::getValue('social_whatsapp'),
                'youtube' => Setting::getValue('social_youtube'),
                'linkedin' => Setting::getValue('social_linkedin'),
            ]),
        ];

        return $this->successResponse($data, 'messages.success');
    }
}

