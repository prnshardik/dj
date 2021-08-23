<?php    
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use App\Models\Item;
    use App\Models\SubItem;
    use App\Models\ItemInventory;
    use App\Models\SubItemInventory;
    use App\Http\Requests\ItemRequest;
    use Auth, Validator, DB, Mail, DataTables, File;

    class TestController extends Controller{
        /** index */
            public function fix_item_qr(Request $request){
                $item = Item::select('id')->get();
                if($item->isNotEmpty()){
                    foreach($item AS $row){
                        _generate_qrcode($row->id ,'item');
                    }
                }
            }

            public function fix_subitem_qr(Request $request){
                ini_set('max_execution_time', 9999);
                $item = SubItem::select('id')->get();
                if($item->isNotEmpty()){
                    foreach($item AS $row){
                        _generate_qrcode($row->id ,'sub_item');
                    }
                }
            }

            public function fix_item_inventory_qr(Request $request){
                ini_set('max_execution_time', 9999);
                $item = ItemInventory::select('id')->get();
                if($item->isNotEmpty()){
                    foreach($item AS $row){
                        _generate_qrcode($row->id ,'item_inventory');
                    }
                }
            }

            public function fix_subitem_inventory_qr(Request $request){
                ini_set('max_execution_time', 9999);
                $item = SubItemInventory::select('id')->get();
                if($item->isNotEmpty()){
                    foreach($item AS $row){
                        _generate_qrcode($row->id ,'sub_item_inventory');
                    }
                }
            }
        /** index */
    }