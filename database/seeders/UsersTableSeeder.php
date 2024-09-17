<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user=[
            
            'name'=>'zein',
            'email'=>'z@gmail.com',
            'password' => Hash::make('123456'),
            'is_admin'=>'1',

   ];
   User::create($user);
    }
}
