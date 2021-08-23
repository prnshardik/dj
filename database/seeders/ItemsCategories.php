<?php

    namespace Database\Seeders;
    use App\Models\ItemCategory;

    use Illuminate\Database\Seeder;

    class ItemsCategories extends Seeder{

        public function run(){
            for($i=1; $i < 6; $i++){
                ItemCategory::create([
                    'title' => "Category $i",
                    'description' => 'Lorem ipsum de tenor',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 1
                ]);
            }
        }
    }
