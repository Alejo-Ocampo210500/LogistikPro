<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SoftnovaUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('SEEDER_SOFTNOVA_PASSWORD', 'X27carjo');

        $users = [
            [
                'name' => 'Alejo Ocampo',
                'email' => 'alejo.ocampo@softnova.com',
            ],
            [
                'name' => 'Jose Vasquez',
                'email' => 'jose.vasquez@softnova.com',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make($password),
                ],
            );
        }
    }
}
