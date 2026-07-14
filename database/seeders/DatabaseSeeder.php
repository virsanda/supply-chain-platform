<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CountriesSeeder::class,
            PortsSeeder::class,
            PositiveWordsSeeder::class,
            NegativeWordsSeeder::class,
            SystemSettingsSeeder::class,
        ]);
        $this->command->info('');
        $this->command->info('✅  Seeded! Admin: admin@supplychain.com / Admin@1234');
        $this->command->info('         User : user@supplychain.com  / User@1234');
    }
}
