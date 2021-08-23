<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder{
    public function run(){
        $this->call([
            UserSeeder::class,
            ItemsCategories::class,
            Items::class,
            SubItemsCategories::class,
            SubItems::class,
            ItemsInventories::class,
            SubItemsInventories::class,
        ]);
    }
}
