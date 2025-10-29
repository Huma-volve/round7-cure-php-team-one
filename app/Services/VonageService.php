<?php
namespace App\Services;

use Log;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;

class VonageService
{
    public static function sendSms($to, $text)
    {
        $basic  = new Basic(env('VONAGE_KEY'), env('VONAGE_SECRET'));
        $client = new Client($basic);

        try {
            $response = $client->sms()->send(
                new \Vonage\SMS\Message\SMS($to, env('VONAGE_FROM'), $text)
            );

            return $response->current()->getStatus();
        } catch (\Exception $e) {
            Log::error('Vonage SMS failed: '.$e->getMessage());
            return false;
        }
    }
}
