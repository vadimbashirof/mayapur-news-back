<?php

namespace Database\Seeders;

use App\Models\AdminModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $name = $this->command->ask('Введите имя администратора', 'superadmin');
        $email = $this->command->ask('Введите email администратора', 'Einomrah@gmail.com');
        $password = $this->command->ask('Введите пароль администратора', 'secret');

        AdminModel::create([
            'login' => $name,
            'email' => $email,
            'role' => AdminModel::SUPER_ADMIN_ROLE,
            'password' => Hash::make($password),
        ]);
    }
}
