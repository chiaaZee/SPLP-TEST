<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

trait RecaptchaValidation
{
    public function validateRecaptcha($response)
    {
        if (is_null($response)) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => ['Harap centang captcha terlebih dahulu.'],
            ]);
        }

        $verify = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $response,
        ]);

        if (!$verify->json('success')) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => ['Verifikasi captcha gagal. Silakan coba lagi.'],
            ]);
        }
    }
}
