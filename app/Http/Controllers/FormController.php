<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
    public function form(): Response
    {
        return response()->view("form");
    }


    // menggunakan custom request
    // untuk validasi menggunakan method validated() tanpa menyebutkan rules nya karena sudah ada di custom request LoginRequest
    public function submitForm(LoginRequest $request): Response
    {
        $data = $request->validated();

        Log::info(json_encode($request->all(), JSON_PRETTY_PRINT));

        return response("OK", Response::HTTP_OK);
    }


    // method validate() pada class Request digunakan untuk validasi data request yang dikirim dari user, misal dari form atau query parameter
    // jika terjadi error akan throw validation exception
    public function login(Request $request): Response
    {
        try {
            $rules = [
                "username" => ["required"],
                "password" => ["required"]
            ];

            $data = $request->validate($rules);

            return response("OK", Response::HTTP_OK);
        } catch (ValidationException $validationException) {
            return response($validationException->errors(), Response::HTTP_BAD_REQUEST);
        }
    }
}
