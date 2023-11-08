<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->insert([
            [
                'name' => 'マネージャ',
                'email' => 'manage@exsample.com',
                'role' => '1',
                'password' => 'manage'
            ],
            [
                'name' => 'スタッフ',
                'email' => 'staff@exsample.com',
                'role' => '2',
                'password' => 'staff'
            ]
        ]);
    }
}
