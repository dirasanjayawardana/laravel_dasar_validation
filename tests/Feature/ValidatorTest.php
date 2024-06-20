<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ValidatorTest extends TestCase
{
    // macam-macam default validation rules:
    // reuired, date, email, min, max


    // Validator::make(); --> membuat instance validator, harus ada data dan membuat aturan atau rules nya
    public function testValidator()
    {
        $data = [
            "username" => "admin",
            "password" => "123445"
        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        // membuat object dari validator
        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        // menjalankan validasi, method passes() akan return true jika validasi sukses
        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());
    }


    public function testValidatorInvalid()
    {
        $data = [
            "username" => "",
            "password" => ""
        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        // membuat object dari validator
        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        // menjalankan validasi, method passes() akan return true jika validasi sukses
        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        // mengambil detail error
        $message = $validator->getMessageBag();

        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }


    // mengembalikan Exception ketika melakukan vlaidasi, ketika data tidak valid akan throw ValidationException
    // menggunakan method: validate(); akan mengembalikan data yg falid saja
    public function testValidatorValidationException()
    {
        $data = [
            "username" => "",
            "password" => ""
        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        try {
            $validator->validate();
            self::fail("ValidationException not thrown");
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }


    // multiple rules, bisa menggunakan | atau dijadikan dalam satu array
    public function testValidatorMultipleRules()
    {
        App::setLocale("id");

        $data = [
            "username" => "dira",
            "password" => "dira"
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => ["required", "min:6", "max:20"]
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();

        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
}
