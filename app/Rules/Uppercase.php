<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Uppercase implements ValidationRule
{
    // Custom Rule
    // $attribute (nama atributnya), $value (valunye), $fail (jika gagal, panggil $fail(messagenya))
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== strtoupper($value)) {
            // $fail("The $attribute mush be UPPERCASE");

            // menggunakan PotentiallyTranslatedString (menggunakan message dari file validation.php)
            // menggunakan message dari 'custom.uppercase' pada file lang/en/validation.php
            $fail("validation.custom.uppercase")->translate([
                "attribute" => $attribute,
                "value" => $value
            ]);
        }
    }
}
