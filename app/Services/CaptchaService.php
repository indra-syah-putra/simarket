<?php

namespace App\Services;

class CaptchaService
{
    public function generate(): array
    {
        $num1 = rand(1, 20);
        $num2 = rand(1, 10);

        session(['captcha_answer' => $num1 + $num2]);

        return [
            'question' => "$num1 + $num2 = ?",
        ];
    }

    public function validate(string $input): bool
    {
        $answer = session('captcha_answer');
        session()->forget('captcha_answer');

        if ($answer === null) {
            return false;
        }

        return (int) $input === $answer;
    }
}
