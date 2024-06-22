<?php

namespace Tests\Feature;

use App\Rules\RegistrationRule;
use App\Rules\Uppercase;
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


    // multiple rules, bisa menggunakan tanda | atau dijadikan dalam satu array
    public function testValidatorMultipleRules()
    {
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


    // method: validate(); akan mengembalikan data yg divalidasi dan valid saja
    public function testValidatorValidData()
    {
        $data = [
            "username" => "user@emai.com",
            "password" => "rahasia",
            "admin" => true,
            "others" => "xxx"
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => "required|min:6|max:20"
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        try {
            $valid = $validator->validate();
            Log::info(json_encode($valid, JSON_PRETTY_PRINT));
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }


    // Validator Message (setiap rules pada validation memiliki validation message, defaultnya dalam bahasa inggris)
    // semua message disimpan di folder lang/{locale}/validation.php, untuk memunculkannya "php artisan lang:publish"
    // jika ingin menambahkan custom message bisa tambahkan di file validatin.php pada field 'custom'
    // untuk menambahkan bahasa lain dengan membuat folder kode lcal di folder lang
    // untuk menggunakannya dengan Facade App::setLocale("kodeLocal")
    public function testValidationCustomMessage()
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


    // membuat custom message langsung saat membuat validator (tidak begitu disarankan)
    // dengan Validator::make(data, rules, messages);
    public function testValidatorInlineMessage()
    {
        $data = [
            "username" => "dira",
            "password" => "dira"
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => ["required", "min:6", "max:20"]
        ];

        $messages = [
            "required" => ":attribute harus diisi",
            "email" => ":attribute harus berupa email",
            "min" => ":attribute minimal :min karakter",
            "max" => ":attribute maksimal :max karakter",
        ];

        $validator = Validator::make($data, $rules, $messages);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();

        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }


    // Additional Validation (melakukan validasi tambahan setelah validasi sebelumnya selesai)
    // menggunakan method: after(callback)
    public function testValidatorAdditionalValidation()
    {
        $data = [
            "username" => "dirapp@email.com",
            "password" => "dirapp@email.com"
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => ["required", "min:6", "max:20"]
        ];

        $validator = Validator::make($data, $rules);
        $validator->after(function (\Illuminate\Validation\Validator $validator){
            $data = $validator->getData();
            if($data['username'] == $data['password']){
                // menambahkan error pada validator dengan key "password"
                $validator->errors()->add("password", "Password tidak boleh sama dengan username");
            }
        });
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();

        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }


    // Custom Rule (membuat aturan validasi sendiri), akan tersimpan di folder app/Rules
    // dengan menggunakan: php artisan make:rule NamaRule
    public function testValidatorCustomRule()
    {
        $data = [
            "username" => "diapp@email.com",
            "password" => "diapp@email.com"
        ];

        $rules = [
            "username" => ["required", "email", "max:100", new Uppercase()],
            "password" => ["required", "min:6", "max:20", new RegistrationRule()]
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();

        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }


    // Custom function rule (membuat custom rule secara langsung tanpa membuat class rule)
    // menggunakan function dengan 3 paramter, $attribute (nama atributnya), $value (valunye), $fail (jika gagal, panggil $fail(messagenya))
    public function testValidatorCustomFunctionRule()
    {
        $data = [
            "username" => "diapp@email.com",
            "password" => "diapp@email.com"
        ];

        $rules = [
            "username" => ["required", "email", "max:100", function(string $attribute, string $value, \Closure $fail){
                if(strtoupper($value) != $value){
                    $fail("The field $attribute must be UPPERCASE");
                }
            }],
            "password" => ["required", "min:6", "max:20", new RegistrationRule()]
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();

        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
}
