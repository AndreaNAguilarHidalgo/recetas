<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Andrea',
            'email' => 'correo@correo.com',
            'password' => Hash::make('12345678'),
            'url' => 'http://andyah.com',
        ]);

        $user2 = User::create([
            'name' => 'Andy',
            'email' => 'correo1@correo.com',
            'password' => Hash::make('12345678'),
            'url' => 'http://andyah1.com',
        ]);

    }
}
