<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialUser extends Seeder
{

    public function run(): void
    {
        User::create([
            'name' => 'ADMIN',
            'email' => 'thiago_barroso@estudante.sesisenai.org.br',
            'password' => env("DEFAULT_PASSWORD"),
            'profile_id' => 1
        ]);
    }
}
