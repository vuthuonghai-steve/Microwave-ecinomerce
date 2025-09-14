<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            return;
        }

        Address::firstOrCreate([
            'user_id' => $user->id,
            'line1' => '123 Đường ABC',
            'district' => 'Quận 1',
            'city' => 'Hồ Chí Minh',
        ], [
            'full_name' => $user->name,
            'phone' => '0900000000',
            'country_code' => 'VN',
            'is_default' => true,
        ]);
    }
}

