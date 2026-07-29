<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users=[
            [
                'name'=>'Admin',
                'email'=>'admin@gmail.com',
                'password'=>Hash::make('password'),
                'role'=>'admin',
                'is_active'=>1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name'=>'staf',
                'email'=>'staf@gmail.com',
                'password'=>Hash::make('password'),
                'role'=>'staf',
                'is_active'=>1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name'=>'kasi',
                'email'=>'kasi@gmail.com',
                'password'=>Hash::make('password'),
                'role'=>'kasi',
                'is_active'=>1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name'=>'kabid',
                'email'=>'kabid@gmail.com',
                'password'=>Hash::make('password'),
                'role'=>'kabid',
                'is_active'=>1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name'=>'kasubag',
                'email'=>'kasubag@gmail.com',
                'password'=>Hash::make('password'),
                'role'=>'kasubag',
                'is_active'=>1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name'=>'kepalabidang',
                'email'=>'kepalabidang@gmail.com',
                'password'=>Hash::make('password'),
                'role'=>'kepalabidang',
                'is_active'=>1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
