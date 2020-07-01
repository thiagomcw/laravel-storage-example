<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    public function testStoreRequiredValidation()
    {
        $this
            ->sendStoreRequest()
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                    'email',
                    'password',
                ],
            ]);
    }

    public function testStoreSpecificValidation()
    {
        $this
            ->sendStoreRequest([
                'name'                  => Str::random(120),
                'email'                 => Str::random(50),
                'password'              => '123456',
                'password_confirmation' => '12345',
            ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                    'email',
                    'password',
                ],
            ]);
    }

    public function testStoreUniqueEmailValidation()
    {
        $user = factory(User::class)->create();

        $this
            ->sendStoreRequest([
                'name'                  => Str::random(100),
                'email'                 => $user->email,
                'password'              => '123456',
                'password_confirmation' => '123456',
            ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'email',
                ],
            ]);
    }

    private function sendStoreRequest(array $data = [])
    {
        return $this->postJson(route('users.store'), $data);
    }
}