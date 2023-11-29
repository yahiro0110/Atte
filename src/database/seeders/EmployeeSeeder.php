<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 初期データを配列にする
        $initialEmployees = [
            [
                'name' => 'マネージャ',
                'email' => 'manager@example.com',
                'role' => '1',
                'password' => Hash::make('password-manager')
            ],
            [
                'name' => 'スタッフ',
                'email' => 'staff@example.com',
                'role' => '2',
                'password' => Hash::make('password-staff')
            ]
        ];

        // 各初期データを挿入する
        foreach ($initialEmployees as $employee) {
            Employee::create($employee);
        }

        // ファクトリーを使った追加データの生成
        Employee::factory()->count(28)->create();
    }
}
