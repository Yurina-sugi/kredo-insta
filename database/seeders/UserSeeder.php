<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'      => 'Hayato',
            'email'     => 'hayato@gmail.com',
            'password'  => Hash::make('hayato12345'),
            'role_id'   => 2
        ]);
        User::create([
            'name'      => 'Yuki',
            'email'     => 'yuki@gmail.com',
            'password'  => Hash::make('yuki12345'),
            'role_id'   => 2
        ]);
        User::create([
            'name'      => 'Yurina',
            'email'     => 'yurina@gmail.com',
            'password'  => Hash::make('yurina12345'),
            'role_id'   => 2
        ]);
        User::create([
            'name'      => 'Miki',
            'email'     => 'miki@gmail.com',
            'password'  => Hash::make('miki12345'),
            'role_id'   => 2
        ]);
        User::create([
            'name'      => 'Wajeeh',
            'email'     => 'wajeeh@gmail.com',
            'password'  => Hash::make('wajeeh12345'),
            'role_id'   => 2
        ]);
    }
}
