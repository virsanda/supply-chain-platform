<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(['email'=>'admin@supplychain.com'],
            ['name'=>'Administrator','email'=>'admin@supplychain.com','password'=>Hash::make('Admin@1234'),'role'=>'admin','is_active'=>true,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('users')->updateOrInsert(['email'=>'user@supplychain.com'],
            ['name'=>'Demo User','email'=>'user@supplychain.com','password'=>Hash::make('User@1234'),'role'=>'user','is_active'=>true,'created_at'=>now(),'updated_at'=>now()]);
    }
}
