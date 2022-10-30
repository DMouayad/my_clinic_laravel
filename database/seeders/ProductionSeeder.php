<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;

    class ProductionSeeder extends Seeder
    {
        /**
         * Seed the application's database.
         *
         * @return void
         */
        public function run(){
            $this->call([
                RoleSeeder::class,
                AdminUserSeeder::class,
            ]);
        }
    }
