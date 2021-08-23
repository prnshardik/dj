<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Cart;
    use App\Models\CartUser;
    use App\Models\CartInventory;
    use App\Models\CartSubInventory;
    use App\Models\User;
    use App\Models\ItemInventoryItem;
    use App\Models\SubItemInventoryItem;
    use Auth, DB, Validator, File ;

    class DashboardController extends Controller{
        /** Index */
            public function index(Request $request){
                $data = Cart::select('id', 'party_name', 'party_address')
                                ->where(['user_id' => auth('sanctum')->user()->id])
                                ->where('status', '!=', 'reach')
                                ->first();

                if($data){
                    $data->user_id = auth('sanctum')->user()->id; 
                    $data->user_name = auth('sanctum')->user()->name; 

                    $cart_users = CartUser::select('cart_users.user_id', 'users.name as user_name')
                                                ->leftjoin('users', 'users.id', 'cart_users.user_id')
                                                ->where(['cart_users.cart_id' => $data->id])
                                                ->get();

                    if($cart_users->isNotEmpty())
                        $data->users = $cart_users;
                    else
                        $data->users = collect();

                    $cart_inventories = CartInventory::select('cart_inventories.inventory_id', 'items_inventories.title')
                                                    ->leftjoin('items_inventories', 'items_inventories.id', 'cart_inventories.inventory_id')
                                                    ->where(['cart_inventories.cart_id' => $data->id])
                                                    ->get();

                    if($cart_inventories->isNotEmpty()){
                        foreach($cart_inventories as $row){
                            $row->items = ItemInventoryItem::select('items_inventories_items.item_id', 'items.status')
                                                                ->leftjoin('items', 'items.id', 'items_inventories_items.item_id')
                                                                ->where(['items_inventories_items.item_inventory_id' => $row->inventory_id])
                                                                ->get();
                        }

                        $data->inventories = $cart_inventories;
                    }else{
                        $data->inventories = collect();
                    }

                    $cart_sub_inventories = CartSubInventory::select('cart_sub_inventories.sub_inventory_id', 'sub_items_inventories.title')
                                                                ->leftjoin('sub_items_inventories', 'sub_items_inventories.id', 'cart_sub_inventories.sub_inventory_id')
                                                                ->where(['cart_sub_inventories.cart_id' => $data->id])
                                                                ->get();

                    if($cart_sub_inventories->isNotEmpty()){
                        foreach($cart_sub_inventories as $row){
                            $row->items = subItemInventoryItem::select('sub_items_inventories_items.sub_item_id', 'sub_items.status')
                                                                ->leftjoin('sub_items', 'sub_items.id', 'sub_items_inventories_items.sub_item_id')
                                                                ->where(['sub_items_inventories_items.sub_item_inventory_id' => $row->sub_inventory_id])
                                                                ->get();
                        }

                        $data->sub_inventories = $cart_sub_inventories;
                    }else{
                        $data->sub_inventories = collect();
                    }

                    return response()->json(['status' => 200, 'message' => 'Record found', 'data' => $data]);
                }else{
                    return response()->json(['status' => 201, 'message' => 'No record found']);
                }                
            }
        /** Index */   
    }
