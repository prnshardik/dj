<?php

    namespace Database\Seeders;
    use App\Models\Item;

    use Illuminate\Database\Seeder;

    class Items extends Seeder{

        public function run(){
            $items = ['1', '2', '3', '4', '5'];

            for($i=1; $i < 16; $i++){
                $item = array_rand($items);

                Item::create([
                    'category_id' => $items[$item],
                    'name' => "Item $i",
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
