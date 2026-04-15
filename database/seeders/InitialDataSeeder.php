<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDefaultUser(
            env('INIT_ADMIN_EMAIL', 'admin@example.com'),
            [
                'role' => 0,
                'name' => env('INIT_ADMIN_NAME', 'Serkan Demir'),
                'pass' => md5(env('INIT_ADMIN_PASSWORD', 'password')),
                'phone' => '05321234567',
                'address' => 'Kuyumcukent Ticaret Merkezi Bahcelievler Istanbul',
                'admin' => 1,
            ]
        );

        $this->createDefaultUser(
            env('INIT_CUSTOMER_EMAIL', 'customer@example.com'),
            [
                'role' => 1,
                'name' => env('INIT_CUSTOMER_NAME', 'Zeynep Kaya'),
                'pass' => md5(env('INIT_CUSTOMER_PASSWORD', 'password')),
                'phone' => '05301112233',
                'address' => 'Ataturk Mah. Ihlamur Sok. No:12 D:4 Bornova Izmir',
                'admin' => 0,
            ]
        );
    }

    protected function createDefaultUser($mail, array $attributes)
    {
        $user = User::withTrashed()->where('mail', $mail)->first();

        if (!$user) {
            User::forceCreate(array_merge($attributes, ['mail' => $mail]));
            return;
        }

        if ($user->trashed()) {
            $user->restore();
        }

        $user->forceFill($attributes);
        $user->save();
    }
}
