<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Cart;
    use App\Models\CartUser;
    use App\Models\CartInventory;
    use App\Models\CartSubInventory;
    use App\Models\Item;
    use App\Models\SubItem;
    use App\Models\User;
    use App\Models\Log;
    use Auth, DB, Validator, File ;

    class ProcessController extends Controller{
        /** scan */
            public function scan(Request $request){
                $rules = ['id' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $data = _qrcode($request->id);

                if($data)
                    return response()->json(['status' => 200, 'message' => 'Record found']);
                else
                    return response()->json(['status' => 201, 'message' => 'No record found']);
            }
        /** scan */

        /** process */
            public function process(Request $request){
                $rules = ['id' => 'required', 'stage' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                if($request->stage == 'dispatch'){
                    DB::beginTransaction();
                    try {
                        $update = Cart::where(['id' => $request->id])->update(['status' => $request->stage, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                        if($update){
                            $crud = [
                                'user_id' => auth('sanctum')->user()->id,
                                'item_id' => $request->id,
                                'item_type' => 'cart',
                                'type' => 'order',
                                'status' => 'dispatch',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $log = Log::insertGetId($crud);

                            if($log){
                                DB::commit();
                                return response()->json(['status' => 200, 'message' => 'Status changes successfully']);
                            }else{
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Failed to log']);
                            }
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Failed to status change']);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Something went wrong, please try again later']);
                    }
                }elseif($request->stage == 'deliver'){
                    DB::beginTransaction();
                    try {
                        $update = Cart::where(['id' => $request->id])->update(['status' => $request->stage, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                        if($update){
                            $crud = [
                                'user_id' => auth('sanctum')->user()->id,
                                'item_id' => $request->id,
                                'item_type' => 'cart',
                                'type' => 'order',
                                'status' => 'deliver',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $log = Log::insertGetId($crud);

                            if($log){
                                DB::commit();
                                return response()->json(['status' => 200, 'message' => 'Status changes successfully']);
                            }else{
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Failed to log']);
                            }
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Failed to status change']);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Something went wrong, please try again later']);
                    }
                }elseif($request->stage == 'return'){
                    DB::beginTransaction();
                    try {
                        $update = Cart::where(['id' => $request->id])->update(['status' => $request->stage, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                        if($update){
                            $crud = [
                                'user_id' => auth('sanctum')->user()->id,
                                'item_id' => $request->id,
                                'item_type' => 'cart',
                                'type' => 'order',
                                'status' => 'return',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $log = Log::insertGetId($crud);

                            if($log){
                                DB::commit();
                                return response()->json(['status' => 200, 'message' => 'Status changes successfully']);
                            }else{
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Failed to log']);
                            }
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Failed to status change']);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Something went wrong, please try again later']);
                    }
                }elseif($request->stage == 'reach'){
                    DB::beginTransaction();
                    try {
                        $update = Cart::where(['id' => $request->id])->update(['status' => $request->stage, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                        if($update){
                            $update_users = CartUser::where(['cart_id' => $request->id])->update(['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                            if(!$update_users){
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                            }

                            $update_inventories = CartInventory::where(['cart_id' => $request->id])->update(['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                            if(!$update_inventories){
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                            }

                            $update_sub_inventories = CartSubInventory::where(['cart_id' => $request->id])->update(['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                            if(!$update_sub_inventories){
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                            }

                            $crud = [
                                'user_id' => auth('sanctum')->user()->id,
                                'item_id' => $request->id,
                                'item_type' => 'cart',
                                'type' => 'order',
                                'status' => 'reach',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $log = Log::insertGetId($crud);

                            if(!$log){
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Failed to log']);
                            }

                            DB::commit();
                            return response()->json(['status' => 200, 'message' => 'Record status changed successfully']);
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                    }
                }elseif($request->stage == 'redispatch'){
                    DB::beginTransaction();
                    try {
                        $get_cart = Cart::where(['id' => $request->id])->first();
                        $get_cart_users = CartUser::select('user_id')->where(['cart_id' => $request->id])->get();
                        $get_cart_inventory = CartInventory::where(['cart_id' => $request->id])->get();
                        $get_cart_subinventory = CartSubInventory::where(['cart_id' => $request->id])->get();

                        $inactive_cart = Cart::where(['id' => $request->id])->update(['status' => 'reach', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                        if(!$inactive_cart){
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Can not inactive cart, try again later']);
                        }

                        $inactive_cart_users = CartUser::where(['cart_id' => $request->id])->update(['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                        if(!$inactive_cart_users){
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Can not inactive cart users, try again later']);
                        }

                        $inactive_cart_inventories = CartInventory::where(['cart_id' => $request->id])->update(['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                        if(!$inactive_cart_inventories){
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Can not inactive cart inventory, try again later']);
                        }

                        $inactive_cart_sub_inventories = CartSubInventory::where(['cart_id' => $request->id])->update(['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                        if(!$inactive_cart_sub_inventories){
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Can not inactive cart sub inventory, try again later']);
                        }

                        $addNewCart = Cart::insertGetId(['redispatch_id' => $get_cart->id ,'user_id' => $get_cart->user_id, 'party_name' => $get_cart->party_name ,'party_address' => $get_cart->party_address,'status' => 'redispatch', 'created_at' => date('Y-m-d H:i:s') ,'created_by' => auth('sanctum')->user()->id , 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                        if($addNewCart){
                           foreach($get_cart_users as $row){
                                $sub_users_crud = [
                                    'cart_id' => $addNewCart,
                                    'user_id' => $row->user_id,
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth('sanctum')->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth('sanctum')->user()->id
                                ];

                                $cart_user_id = CartUser::insertGetId($sub_users_crud);

                                if(empty($cart_user_id)){
                                    DB::rollback();
                                    return response()->json(['code' => 201, 'message' => 'Cart sub users insert error, please try again later']);
                                }
                            }

                            foreach($get_cart_inventory as $row){
                                $inventories_crud = [
                                    'cart_id' => $addNewCart,
                                    'inventory_id' => $row->inventory_id,
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth('sanctum')->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth('sanctum')->user()->id
                                ];

                                $cart_inventory_id = CartInventory::insertGetId($inventories_crud);

                                if(empty($cart_inventory_id)){
                                    DB::rollback();
                                    return response()->json(['code' => 201, 'message' => 'Cart inventory insert error, please try again later']);
                                }
                            }

                            foreach($get_cart_subinventory as $row){
                                $sub_inventories_crud = [
                                    'cart_id' => $addNewCart,
                                    'sub_inventory_id' => $row->sub_inventory_id,
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth('sanctum')->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth('sanctum')->user()->id
                                ];

                                $cart_sub_inventory_id = CartSubInventory::insertGetId($sub_inventories_crud);

                                if(empty($cart_sub_inventory_id)){
                                    DB::rollback();
                                    return response()->json(['code' => 201, 'message' => 'Cart sub inventory insert error, please try again later']);
                                }
                            }

                             $crud = [
                                'user_id' => auth('sanctum')->user()->id,
                                'item_id' => $request->id,
                                'item_type' => 'cart',
                                'type' => 'order',
                                'status' => 'reach',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $log = Log::insertGetId($crud);

                            if(!$log){
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Failed to log']);
                            }

                            $crudtwo = [
                                'user_id' => auth('sanctum')->user()->id,
                                'item_id' => $addNewCart,
                                'item_type' => 'cart',
                                'type' => 'order',
                                'status' => 'redispatch',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $logtwo = Log::insertGetId($crudtwo);

                            if(!$logtwo){
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Failed to log']);
                            }
                            $data = [
                                'new_cart_id' => $addNewCart
                            ];
                            DB::commit();
                            return response()->json(['status' => 200, 'message' => 'Status changes successfully' ,'data' =>$data]);
                           
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Failed to status change']);
                        }

                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Something went wrong, please try again later']);
                    }
                }else{
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                }
            }
        /** process */

        /** maintenance */
            public function maintenance(Request $request){
                $rules = ['id' => 'required', 'stage' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $input = explode('-', $request->id);

                if(empty($input[0]))
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                else
                    $folder = $input[0];
    
                if(empty($input[1]))
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                else
                    $id = $input[1];
    
                if($folder == 'item')
                    $table = 'items';
                elseif($folder == 'subItem')
                    $table = 'sub_items';
                else
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                
                if($request->stage == 'dispatch'){
                    DB::beginTransaction();
                    try {
                        $update = DB::table($table)->where(['id' => $id])->update(['status' => 'repairing', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                        if($update){
                            $crud = [
                                'user_id' => auth('sanctum')->user()->id,
                                'item_id' => $id,
                                'item_type' => $table,
                                'type' => 'repair',
                                'status' => 'dispatch',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $log = Log::insertGetId($crud);

                            if($log){
                                DB::commit();
                                return response()->json(['status' => 200, 'message' => 'Status changes successfully']);
                            }else{
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Failed to log']);
                            }
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Failed to status change']);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Something went wrong, please try again later']);
                    }
                }elseif($request->stage == 'deliver'){
                    $crud = [
                        'user_id' => auth('sanctum')->user()->id,
                        'item_id' => $id,
                        'item_type' => $table,
                        'type' => 'repair',
                        'status' => 'deliver',
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth('sanctum')->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth('sanctum')->user()->id
                    ];
                    
                    $log = Log::insertGetId($crud);

                    if($log){
                        return response()->json(['status' => 200, 'message' => 'Status changes successfully']);
                    }else{
                        return response()->json(['status' => 201, 'message' => 'Failed to log']);
                    }
                }elseif($request->stage == 'return'){
                    $crud = [
                        'user_id' => auth('sanctum')->user()->id,
                        'item_id' => $id,
                        'item_type' => $table,
                        'type' => 'repair',
                        'status' => 'deliver',
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth('sanctum')->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth('sanctum')->user()->id
                    ];
                    
                    $log = Log::insertGetId($crud);

                    if($log){
                        return response()->json(['status' => 200, 'message' => 'Status changes successfully']);
                    }else{
                        return response()->json(['status' => 201, 'message' => 'Failed to log']);
                    }
                }elseif($request->stage == 'reach'){
                    DB::beginTransaction();
                    try {
                        $update = DB::table($table)->where(['id' => $id])->update(['status' => 'active', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
            
                        if($update){
                            $crud = [
                                'user_id' => auth('sanctum')->user()->id,
                                'item_id' => $id,
                                'item_type' => $table,
                                'type' => 'repair',
                                'status' => 'reach',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];
                            
                            $log = Log::insertGetId($crud);

                            if($log){
                                DB::commit();
                                return response()->json(['status' => 200, 'message' => 'Status changes successfully']);
                            }else{
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Failed to log']);
                            }
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Failed to status change']);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Something went wrong, please try again later']);
                    }
                }else{
                    return response()->json(['status' => 201, 'message' => 'Somthing went wrong']);
                }
            }
        /** maintenance */
    }
