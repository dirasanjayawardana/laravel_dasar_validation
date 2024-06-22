<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    // method validate() pada class Request digunakan untuk validasi data request yang dikirim dari user, misal dari form atau query parameter
    // jika terjadi error akan throw validation exception
    public function testLoginFailed()
    {
        $response = $this->post('/form/login', [
            'username' => '',
            'password' => ''
        ]);
        $response->assertStatus(400);
    }

    public function testLoginSuccess()
    {
        $response = $this->post('/form/login', [
            'username' => 'admin',
            'password' => 'rahasia'
        ]);
        $response->assertStatus(200);
    }

    public function testFormFailed()
    {
        $response = $this->post('/form', [
            'username' => '',
            'password' => ''
        ]);
        $response->assertStatus(302);
    }

    public function testFormSuccess()
    {
        $response = $this->post('/form', [
            'username' => 'admin@email.com',
            'password' => 'rahasia@123'
        ]);
        $response->assertStatus(200);
    }
}
