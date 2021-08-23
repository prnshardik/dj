<?php

    namespace Database\Seeders;
    use App\Models\ItemInventory;
    use App\Models\ItemInventoryItem;

    use Illuminate\Database\Seeder;

    class ItemsInventories extends Seeder{

        public function run(){
            $items = 0;

            for($i=1; $i < 6; $i++){
                $id = ItemInventory::insertGetId([
                    'title' => "Box $i",
                    'description' => 'Lorem ipsum de tenor',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 1
                ]);

                for($j=1; $j<3; $j++){
                    $items++;
                    ItemInventoryItem::insertGetId([
                        'item_inventory_id' => $id,
                        'item_id' => $items,
                        'status' => 'active',
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => 1
                    ]);
                }
            }
        }
    }
