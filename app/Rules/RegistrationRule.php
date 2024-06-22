<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Validation\Validator;

class RegistrationRule implements ValidationRule, DataAwareRule, ValidatorAwareRule
{
    // DataAwareRule (agar custom rule yang dibuat biasa melihat seluruh data yang divalidasi)
    // ValidatorAwareRule (agar bisa mengguanakan object Validator, dicustom rule)
    private array $data;
    private Validator $validator;

    // set data yang akan divalidasi
    public function setData(array $data): RegistrationRule
    {
        $this->data = $data;
        return $this;
    }

    // set validator agar bisa digunakan di custom rule
    public function setValidator(Validator $validator): RegistrationRule
    {
        $this->validator = $validator;
        return $this;
    }

    // Custom Rule
    // $attribute (nama atributnya), $value (valunye), $fail (jika gagal, panggil $fail(messagenya))
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = $value;
        $username = $this->data['username'];

        if($password == $username){
            $fail("$attribute must be different with username");
        }
    }
}
