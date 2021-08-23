<?php

    namespace Database\Seeders;
    use App\Models\SubItemCategory;

    use Illuminate\Database\Seeder;

    class SubItemsCategories extends Seeder{

        public function run(){
            for($i=1; $i < 6; $i++){
                SubItemCategory::create([
                    'title' => "Sub Category $i",
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
