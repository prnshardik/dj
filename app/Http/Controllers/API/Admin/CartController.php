<?php

    namespace App\Http\Controllers\API\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Models\Cart;
    use App\Models\CartUser;
    use App\Models\CartInventory;
    use App\Models\CartSubInventory;
    use App\Models\ItemInventory;
    use App\Models\ItemInventoryItem;
    use App\Models\SubItemInventory;
    use App\Models\SubItemInventoryItem;
    use Auth, DB, Validator, File;

    class CartController extends Controller{
        /** index */
            public function index(Request $request){
                $data = Cart::select('cart.id', 'u.name as user_name', 'cart.party_name', 'cart.party_address', 'cart.status')
                                    ->leftjoin('users as u', 'u.id', 'cart.user_id')
                                    ->where('cart.status', '!=', 'reach')
                                    ->orderBy('cart.id','desc')
                                    ->get();

                if($data->isNotEmpty()){
                    foreach($data as $row){
                        $cart_item = CartInventory::select(DB::Raw("COUNT(".'id'.") as count"))->where(['cart_id' => $row->id])->first();
                        $row->items_count = $cart_item->count;
                    }
                }

                if($data->isNotEmpty()){
                    foreach($data as $row){
                        $cart_sub_item = CartSubInventory::select(DB::Raw("COUNT(".'id'.") as count"))->where(['cart_id' => $row->id])->first();
                        $row->sub_items_count = $cart_sub_item->count;
                    }
                }

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No data found']);
            }
        /** index */

        /** single */
            public function single(Request $request, $id=''){
                if($id == '')
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);

                $data = Cart::select('cart.id', 'cart.user_id', 'cart.party_name', 'cart.party_address', 'u.name as user_name')
                            ->leftjoin('users as u', 'u.id', 'cart.user_id')
                            ->where(['cart.id' => $id])
                            ->first();

                if($data){
                    $sub_users = CartUser::select('u.name as name', 'u.id as user_id')
                                            ->leftjoin('users as u', 'u.id', 'cart_users.user_id')
                                            ->where(['cart_users.cart_id' => $data->id])
                                            ->get();

                    if($sub_users->isNotEmpty())
                        $data->sub_users = $sub_users;
                    else
                        $data->sub_users = collect();

                    $inventories = CartInventory::select('i.id', 'i.title')
                                                    ->leftjoin('items_inventories as i', 'i.id', 'cart_inventories.inventory_id')
                                                    ->where(['cart_inventories.cart_id' => $data->id])
                                                    ->get();

                    if($inventories->isNotEmpty()){
                        foreach($inventories as $row){
                            $inventories_items = ItemInventoryItem::where(['item_inventory_id' => $row->id])->count();
                            $row->item = $inventories_items;
                        }
                        $data->inventories = $inventories;
                    }else{
                        $data->inventories = collect();
                    }

                    $sub_inventories = CartSubInventory::select('i.id', 'i.title')
                                                            ->leftjoin('sub_items_inventories as i', 'i.id', 'cart_sub_inventories.sub_inventory_id')
                                                            ->where(['cart_sub_inventories.cart_id' => $data->id])
                                                            ->get();

                    if($sub_inventories->isNotEmpty()){
                        foreach($sub_inventories as $row){
                            $sub_inventories_items = SubItemInventoryItem::where(['sub_item_inventory_id' => $row->id])->count();
                            $row->item = $sub_inventories_items;
                        }
                        $data->sub_inventories = $sub_inventories;
                    }else{
                        $data->sub_inventories = collect();
                    }

                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                }else{
                    return response()->json(['status' => 201, 'message' => 'No data found']);                        
                }
            }
        /** single */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'user_id' => 'required', 
                    'party_name' => 'required', 
                    'party_address' => 'required',
                    'sub_users' => 'required|array|min:1',
                    'inventories' => 'required|array|min:1', 
                    'sub_inventories' => 'required|array|min:1',
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
                    
                $user_id = $request->user_id;
                $party_name = $request->party_name;
                $party_address = $request->party_address;
                $sub_users = $request->sub_users;
                $inventories = $request->inventories;
                $sub_inventories = $request->sub_inventories;

                if($sub_users[0] == null)
                    return response()->json(['status' => 422, 'message' => ['sub_users' => 'Please select atleast one sub user']]);

                if($inventories[0] == null)
                    return response()->json(['status' => 422, 'message' => ['inventories' => 'Please select atleast one inventory']]);

                if($sub_inventories[0] == null)
                    return response()->json(['status' => 422, 'message' => ['sub_inventories' => 'Please select atleast one sub inventory']]);

                DB::beginTransaction();
                try {
                    $crud = [
                        'user_id' => $user_id,
                        'party_name' => $party_name,
                        'party_address' => $party_address,
                        'status' => 'assigned',
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth('sanctum')->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth('sanctum')->user()->id
                    ];
                    
                    $last_id = Cart::insertGetId($crud);

                    if($last_id){
                        foreach($sub_users as $k => $v){
                            $sub_users_crud = [
                                'cart_id' => $last_id,
                                'user_id' => $v,
                                'status' => 'active',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $cart_user_id = CartUser::insertGetId($sub_users_crud);

                            if(empty($cart_user_id)){
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Cart sub users insert error, please try again later']);
                            }
                        }

                        foreach($inventories as $k => $v){
                            $inventories_crud = [
                                'cart_id' => $last_id,
                                'inventory_id' => $v,
                                'status' => 'active',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $cart_inventory_id = CartInventory::insertGetId($inventories_crud);

                            if(empty($cart_inventory_id)){
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Cart inventory insert error, please try again later']);
                            }
                        }

                        foreach($sub_inventories as $k => $v){
                            $sub_inventories_crud = [
                                'cart_id' => $last_id,
                                'sub_inventory_id' => $v,
                                'status' => 'active',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $cart_sub_inventory_id = CartSubInventory::insertGetId($sub_inventories_crud);

                            if(empty($cart_sub_inventory_id)){
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Cart sub inventory insert error, please try again later']);
                            }
                        }

                        DB::commit();
                        return response()->json(['status' => 200, 'message' => 'Cart added successfully']);
                    }else{
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Cart insert error, please try again later']);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 201, 'message' => 'Something went wrong, please try again later']);
                }
            }
        /** insert */

        /** update */
            public function update(Request $request){
                $rules = [
                    'id' => 'required',
                    'user_id' => 'required', 
                    'party_name' => 'required', 
                    'party_address' => 'required',
                    'sub_users' => 'required|array|min:1',
                    'inventories' => 'required|array|min:1', 
                    'sub_inventories' => 'required|array|min:1',
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
                    
                $user_id = $request->user_id;
                $party_name = $request->party_name;
                $party_address = $request->party_address;
                $sub_users = $request->sub_users;
                $inventories = $request->inventories;
                $sub_inventories = $request->sub_inventories;

                if($sub_users[0] == null)
                    return response()->json(['status' => 422, 'message' => ['sub_users' => 'Please select atleast one sub user']]);

                if($inventories[0] == null)
                    return response()->json(['status' => 422, 'message' => ['inventories' => 'Please select atleast one inventory']]);

                if($sub_inventories[0] == null)
                    return response()->json(['status' => 422, 'message' => ['sub_inventories' => 'Please select atleast one sub inventory']]);

                DB::beginTransaction();
                try {
                    $crud = [
                        'user_id' => $user_id,
                        'party_name' => $party_name,
                        'party_address' => $party_address,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth('sanctum')->user()->id
                    ];
                    
                    $update = Cart::where(['id' => $request->id])->update($crud);

                    if($update){
                        CartUser::where(['cart_id' => $request->id])->delete();

                        foreach($sub_users as $k => $v){
                            $sub_users_crud = [
                                'cart_id' => $request->id,
                                'user_id' => $v,
                                'status' => 'active',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $cart_user_id = CartUser::insertGetId($sub_users_crud);

                            if(empty($cart_user_id)){
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Cart sub users insert error, please try again later']);
                            }
                        }

                        foreach($inventories as $k => $v){
                            $exst_inventory = CartInventory::select('id')->where(['cart_id' => $request->id, 'inventory_id' => $v])->first();
                            
                            if(empty($exst_inventory)){
                                $inventories_crud = [
                                    'cart_id' => $request->id,
                                    'inventory_id' => $v,
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth('sanctum')->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth('sanctum')->user()->id
                                ];

                                $cart_inventory_id = CartInventory::insertGetId($inventories_crud);

                                if(empty($cart_inventory_id)){
                                    DB::rollback();
                                    return response()->json(['status' => 201, 'message' => 'Cart inventory insert error, please try again later']);
                                }
                            }
                        }

                        foreach($sub_inventories as $k => $v){
                            $exst_sub_inventory = CartSubInventory::select('id')->where(['cart_id' => $request->id, 'sub_inventory_id' => $v])->first();

                            if(empty($exst_sub_inventory)){
                                $sub_inventories_crud = [
                                    'cart_id' => $request->id,
                                    'sub_inventory_id' => $v,
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth('sanctum')->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth('sanctum')->user()->id
                                ];

                                $cart_sub_inventory_id = CartSubInventory::insertGetId($sub_inventories_crud);

                                if(empty($cart_sub_inventory_id)){
                                    DB::rollback();
                                    return response()->json(['status' => 201, 'message' => 'Cart sub inventory insert error, please try again later']);
                                }
                            }
                        }

                        DB::commit();
                        return response()->json(['status' => 200, 'message' => 'Cart updated successfully']);
                    }else{
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Cart update error, please try again later']);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 201, 'message' => 'Cart update error, please try again later']);
                }
            }
        /** update */

        /** change-status */
            public function status_change(Request $request){
                $rules = [
                    'id' => 'required',
                    'status' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $data = Cart::where(['id' => $request->id])->first();

                if(!empty($data)){
                    $delete = Cart::where(['id' => $request->id])->delete();
                    
                    if($delete){
                        return response()->json(['code' => 200, 'message' =>'Record deleted successfully']);
                    }else{
                        return response()->json(['code' => 201, 'message' =>'Faild to delete record']);
                    }
                }else{
                    return response()->json(['code' => 201, 'message' =>'Faild to delete record']);
                }
            }
        /** change-status */

        /** users */
            public function users(Request $request){
                $request->id = $request->cart_id ?? null;
                $user_id = '';
                
                if($cart_id != null){
                    $cart = Cart::select('user_id')->where(['id' => $cart_id])->first();

                    if(!empty($cart))
                        $user_id = $cart->user_id;
                }

                $data = User::select('id', 'name')
                                ->where(['status' => 'active', 'is_admin' => 'n'])
                                ->whereNotIn('id', function($query) use ($cart_id) {
                                    $query->select('user_id')->from('cart')->where('status', '!=', 'reach')->where('id', '!=', $cart_id); 
                                })
                                ->whereNotIn('id', function($query) use ($cart_id) {
                                    $query->select('user_id')->from('cart_users')->where(['status' => 'active'])->where('cart_id', '!=', $cart_id); 
                                })
                                ->get();

                if($data->isNotEmpty()){
                    foreach($data as $row){
                        $row->selected = false;
                        
                        if(!empty($user_id) && $row->id == $user_id)
                            $row->selected = true;  
                    }

                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                }else{
                    return response()->json(['status' => 201, 'message' => 'No data found']);
                }
            }
        /** users */

        /** sub-users */
            public function sub_users(Request $request){
                $cart_id = $request->cart_id ?? NULL;
                $cart_users = [];

                if(!empty($cart_id))
                    $cart_users = CartUser::select('user_id')->where(['cart_id' => $cart_id])->get()->toArray();
                
                if(!empty($cart_users)){
                    $cart_users = array_map(function($row){
                        return $row['user_id'];
                    }, $cart_users);
                }
                
                $data = User::select('id', 'name')
                            ->where(['status' => 'active', 'is_admin' => 'n'])
                            ->where('id', '!=', $request->id)
                            ->whereNotIn('id', function($query) use ($cart_id) {
                                $query->select('user_id')->from('cart')->where('status', '!=', 'reach')->where('id', '!=', $cart_id); 
                            })
                            ->whereNotIn('id', function($query) use ($cart_id) {
                                $query->select('user_id')->from('cart_users')->where(['status' => 'active'])->where('cart_id', '!=', $cart_id); 
                            })
                            ->get();

                if($data->isNotEmpty()){
                    foreach($data as $row){
                        $row->selected = false;

                        if(!empty($cart_users) && in_array($row->id, $cart_users)) 
                            $row->selected = true;
                    }

                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                }else{
                    return response()->json(['status' => 201, 'message' => 'No data found']);
                }
            }
        /** sub-users */

        /** inventories */
            public function inventories(Request $request){
                $cart_id = $request->cart_id ?? NULL;
                $cart_inventories = [];

                if(!empty($cart_id))
                    $cart_inventories = CartInventory::select('inventory_id')->where(['cart_id' => $cart_id])->get()->toArray();
                
                if(!empty($cart_inventories)){
                    $cart_inventories = array_map(function($row){
                        return $row['inventory_id'];
                    }, $cart_inventories);
                }

                $collection = ItemInventory::select('items_inventories.id', 'items_inventories.title', 
                                                DB::Raw("(select COUNT(items_inventories_items.id) from items_inventories_items where items_inventories_items.item_inventory_id = items_inventories.id) as items")
                                            )
                                            ->where(['items_inventories.status' => 'active']);
                if($cart_id != null){
                    $collection->whereNotIn('items_inventories.id', function($query) use ($cart_id) {
                                    $query->select('inventory_id')->from('cart_inventories')->where('cart_id', '!=', $cart_id)->where('status', '!=', 'inactive'); 
                                });
                }else{
                    $collection->whereNotIn('items_inventories.id', function($query) {
                                    $query->select('inventory_id')->from('cart_inventories')->where('status', '!=', 'inactive'); 
                                });
                }

                $data = $collection->get();
                
                if($data->isNotEmpty()){
                    foreach($data as $row){
                        $row->selected = false;

                        if(!empty($cart_inventories) && in_array($row->id, $cart_inventories))
                            $row->selected = true;
                    }

                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                }else{
                    return response()->json(['status' => 201, 'message' => 'No data found']);
                }
            }
        /** inventories */

        /** sub-inventories */
            public function sub_inventories(Request $request){
                $cart_id = $request->cart_id ?? NULL;
                $cart_inventories = [];

                if(!empty($cart_id))
                    $cart_inventories = CartSubInventory::select('sub_inventory_id')->where(['cart_id' => $cart_id])->get()->toArray();
                
                if(!empty($cart_inventories)){
                    $cart_inventories = array_map(function($row){
                        return $row['sub_inventory_id'];
                    }, $cart_inventories);
                }

                $collection = SubItemInventory::select('sub_items_inventories.id', 'sub_items_inventories.title', 
                                                DB::Raw("(select COUNT(sub_items_inventories_items.id) from sub_items_inventories_items where sub_items_inventories_items.sub_item_inventory_id = sub_items_inventories.id) as items")
                                            )
                                            ->where(['sub_items_inventories.status' => 'active']);
                if($cart_id != null){
                    $collection->whereNotIn('sub_items_inventories.id', function($query) use ($cart_id) {
                                    $query->select('sub_inventory_id')->from('cart_sub_inventories')->where('cart_id', '!=', $cart_id)->where('status', '!=', 'inactive'); 
                                });
                }else{
                    $collection->whereNotIn('sub_items_inventories.id', function($query) {
                                    $query->select('sub_inventory_id')->from('cart_sub_inventories')->where('status', '!=', 'inactive'); 
                                });
                }

                $data = $collection->get();
                
                if($data->isNotEmpty()){
                    foreach($data as $row){
                        $row->selected = false;

                        if(!empty($cart_inventories) && in_array($row->id, $cart_inventories))
                            $row->selected = true;
                    }

                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                }else{
                    return response()->json(['status' => 201, 'message' => 'No data found']);
                }
            }
        /** sub-inventories */

        /** delete-inventories */
            public function delete_inventories(Request $request){
                $rules = ['cart_id' => 'required', 'id' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $exst = CartInventory::where(['cart_id' => $request->cart_id, 'inventory_id' => $request->id])->first();

                if($exst){
                    $delete = CartInventory::where(['cart_id' => $request->cart_id, 'inventory_id' => $request->id])->delete();

                    if($delete)
                        return response()->json(['status' => 200, 'message' => 'Record unchecked successfully']);
                    else
                        return response()->json(['status' => 201, 'message' => 'Failed to uncheck record, please try again later']);
                }else{
                    return response()->json(['status' => 200, 'message' => 'Record unchecked successfully']);
                }
            }
        /** delete-inventories */

        /** delete-sub-inventories */
            public function delete_sub_inventories(Request $request){
                $rules = ['cart_id' => 'required', 'id' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $exst = CartSubInventory::where(['cart_id' => $request->cart_id, 'sub_inventory_id' => $request->id])->first();

                if($exst){
                    $delete = CartSubInventory::where(['cart_id' => $request->cart_id, 'sub_inventory_id' => $request->id])->delete();

                    if($delete)
                        return response()->json(['status' => 200, 'message' => 'Record unchecked successfully']);
                    else
                        return response()->json(['status' => 201, 'message' => 'Failed to uncheck record, please try again later']);
                }else{
                    return response()->json(['status' => 200, 'message' => 'Record unchecked successfully']);
                }
            }
        /** delete-sub-inventories */
    }