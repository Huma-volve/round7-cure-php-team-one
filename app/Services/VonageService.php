<?php

namespace App\Services;

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class VonageService
{
    public static function sendSms($to, $text)
    {
        $basic  = new Basic(config('services.vonage.key'), config('services.vonage.secret'));
        $client = new Client($basic);

       
        if (!str_starts_with($to, '+')) {
            $to = '+2' . $to; 
        }

        $message = new SMS($to, config('services.vonage.from'), $text);
        $response = $client->sms()->send($message);

        return $response;
    }
}
