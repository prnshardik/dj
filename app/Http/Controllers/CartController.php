<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use App\Models\User;
    use App\Models\Cart;
    use App\Models\CartUser;
    use App\Models\CartInventory;
    use App\Models\CartSubInventory;
    use App\Models\ItemInventory;
    use App\Models\ItemInventoryItem;
    use App\Models\SubItemInventory;
    use App\Models\SubItemInventoryItem;
    use Auth, Validator, DB, Mail, DataTables;

    class CartController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Cart::select('cart.id', 'u.name as user_name', 'cart.party_name', 'cart.status')
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

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                $return = '<div class="btn-group btn-sm">
                                            <a href="'.route('cart.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                <i class="fa fa-eye"></i>
                                            </a>';
                                
                                if($data->status == 'assigned'){
                                    $return .= '<a href="'.route('cart.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a>';
                                }

                                $return .= '<a class="btn btn-default btn-xs" href="javascript:;" onclick="change_status(this);" data-id="'.base64_encode($data->id).'"><i class="fa fa-trash"></i></a>
                                        </div>';
                                return $return;
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'assigned')
                                    return '<span class="badge badge-pill badge-info">Assigned</span>';
                                else if($data->status == 'dispatch')
                                    return '<span class="badge badge-pill badge-info">Dispatch</span>';
                                else if($data->status == 'deliver')
                                    return '<span class="badge badge-pill badge-info">Deliver</span>';
                                else if($data->status == 'return')
                                    return '<span class="badge badge-pill badge-info">Return</span>';
                                else if($data->status == 'reach')
                                    return '<span class="badge badge-pill badge-info">Reach</span>';
                                else
                                    return '-';
                            })

                            ->rawColumns(['action', 'status'])
                            ->make(true);
                }
                return view('cart.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
               return view('cart.step');
            }
        /** create */

        /** insert */
            public function insert(Request $request){
                if(!$request->ajax()){ return true; }

                $input = $request->obj;
                
                $validator = Validator::make($input,
                                    [
                                        'user' => 'required|array|min:1', 
                                        'sub_users' => 'required|array|min:1',
                                        'party_name' => 'required', 
                                        'party_address' => 'required',
                                        'inventories' => 'required|array|min:1', 
                                        'sub_inventories' => 'required|array|min:1',
                                    ]
                                );

                if($validator->fails()){
                    return response()->json($validator->errors(), 422);
                }else{
                    $user = array_keys($input['user']);
                    $user = $user[0];
                    $sub_users = array_keys($input['sub_users']);
                    $party_name = $input['party_name'];
                    $party_address = $input['party_address'];
                    $inventories = array_keys($input['inventories']);
                    $sub_inventories = array_keys($input['sub_inventories']);
    
                    DB::beginTransaction();
                    try {
                        $crud = [
                                    'user_id' => $user,
                                    'party_name' => $party_name,
                                    'party_address' => $party_address,
                                    'status' => 'assigned',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth()->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth()->user()->id
                                ];
                        
                        $last_id = Cart::insertGetId($crud);

                        if($last_id){
                            foreach($sub_users as $k => $v){
                                $sub_users_crud = [
                                    'cart_id' => $last_id,
                                    'user_id' => $v,
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth()->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth()->user()->id
                                ];

                                $cart_user_id = CartUser::insertGetId($sub_users_crud);

                                if(empty($cart_user_id)){
                                    DB::rollback();
                                    return response()->json(['code' => 201, 'message' => 'Cart sub users insert error, please try again later']);
                                }
                            }

                            foreach($inventories as $k => $v){
                                $inventories_crud = [
                                    'cart_id' => $last_id,
                                    'inventory_id' => $v,
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth()->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth()->user()->id
                                ];

                                $cart_inventory_id = CartInventory::insertGetId($inventories_crud);

                                if(empty($cart_inventory_id)){
                                    DB::rollback();
                                    return response()->json(['code' => 201, 'message' => 'Cart inventory insert error, please try again later']);
                                }
                            }

                            foreach($sub_inventories as $k => $v){
                                $sub_inventories_crud = [
                                    'cart_id' => $last_id,
                                    'sub_inventory_id' => $v,
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth()->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth()->user()->id
                                ];

                                $cart_sub_inventory_id = CartSubInventory::insertGetId($sub_inventories_crud);

                                if(empty($cart_sub_inventory_id)){
                                    DB::rollback();
                                    return response()->json(['code' => 201, 'message' => 'Cart sub inventory insert error, please try again later']);
                                }
                            }

                            DB::commit();
                            return response()->json(['code' => 200, 'message' => 'Cart added successfully']);
                        }else{
                            DB::rollback();
                            return response()->json(['code' => 201, 'message' => 'Cart insert error, please try again later']);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['code' => 201, 'message' => 'Something went wrong, please try again later']);
                    }
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->back()->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Cart::select('cart.id', 'cart.user_id', 'cart.party_name', 'cart.party_address', 'u.name as user_name')
                                    ->leftjoin('users as u', 'u.id', 'cart.user_id')
                                    ->where(['cart.id' => $id])
                                    ->first();

                if($data){
                    $sub_users = CartUser::select('u.name as name')
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
      
                    return view('cart.view', ['data' => $data]);
                }else{
                    return redirect()->back()->with('error', 'No cart found');
                }
            }
        /** view */

        /** detail */
            public function detail(Request $request){
                $id = $request->id;
                
                if(empty($id))
                    return response()->json(['code' => 201]);

                $data = [];
                
                $cart = Cart::select('cart.id', 'cart.party_name', 'cart.party_address', 'cart.user_id', 'users.name as user_name')
                                ->leftjoin('users', 'users.id', 'cart.user_id')
                                ->where(['cart.id' => $id])
                                ->first();
                    
                if(!empty($cart)){
                    $data['party_name'] = $cart->party_name;
                    $data['party_address'] = $cart->party_address;
                    $data['user'] = [$cart->user_id => $cart->user_name];

                    $subUsers = CartUser::select('cart_users.user_id', 'users.name as user_name')
                                            ->leftjoin('users', 'users.id', 'cart_users.user_id')
                                            ->where(['cart_users.cart_id' => $id])
                                            ->get();   

                    $sub_users = [];
                    if($subUsers->isNotEmpty()){
                        foreach($subUsers as $row){
                            $sub_users[$row->user_id] = $row->user_name; 
                        }
                    }

                    $data['sub_users'] = $sub_users;

                    $cartInventories = CartInventory::select('cart_inventories.inventory_id', 'items_inventories.title', 
                                                            DB::Raw("(select COUNT(".'id'.") from items_inventories_items where items_inventories_items.item_inventory_id = cart_inventories.inventory_id) as count")
                                                            )
                                                        ->leftjoin('items_inventories', 'cart_inventories.inventory_id', 'items_inventories.id')
                                                        ->where(['cart_inventories.cart_id' => $id])
                                                        ->get(); 
                                                        
                    $inventories = [];
                    if($cartInventories->isNotEmpty()){
                        foreach($cartInventories as $row){
                            $inventories[$row->inventory_id] = ['name' => $row->title, 'item' => $row->count]; 
                        }
                    }

                    $data['inventories'] = (object) $inventories;

                    $subCartInventories = CartSubInventory::select('cart_sub_inventories.sub_inventory_id', 'sub_items_inventories.title', 
                                                            DB::Raw("(select COUNT(".'id'.") from sub_items_inventories_items where sub_items_inventories_items.sub_item_inventory_id = cart_sub_inventories.sub_inventory_id) as count")
                                                            )
                                                        ->leftjoin('sub_items_inventories', 'cart_sub_inventories.sub_inventory_id', 'sub_items_inventories.id')
                                                        ->where(['cart_sub_inventories.cart_id' => $id])
                                                        ->get(); 
                                                        
                    $sub_inventories = [];
                    if($subCartInventories->isNotEmpty()){
                        foreach($subCartInventories as $row){
                            $sub_inventories[$row->sub_inventory_id] = ['name' => $row->title, 'item' => $row->count]; 
                        }
                    }

                    $data['sub_inventories'] = (object) $sub_inventories;
                }

                return response()->json(['code' => 200, 'data' => $data]);
            }
        /** detail */

        /** edit */ 
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->back()->with('error', 'Something went wrong');

                $id = base64_decode($id);

                return view('cart.step', ['cart_id' => $id]);
            }
        /** edit */ 

        /** update */
            public function update(Request $request){
                if(!$request->ajax()){ return true; }

                $input = $request->obj;
                
                $validator = Validator::make($input,
                                    [
                                        'cart_id' => 'required',
                                        'user' => 'required|array|min:1', 
                                        'sub_users' => 'required|array|min:1',
                                        'party_name' => 'required', 
                                        'party_address' => 'required',
                                        'inventories' => 'required|array|min:1', 
                                        'sub_inventories' => 'required|array|min:1',
                                    ]
                                );
                if($validator->fails()){
                    return response()->json($validator->errors(), 422);
                }else{
                    $cart_id = $input['cart_id'];
                    $user = array_keys($input['user']);
                    $user = $user[0];
                    $sub_users = array_keys($input['sub_users']);
                    $party_name = $input['party_name'];
                    $party_address = $input['party_address'];
                    $inventories = array_keys($input['inventories']);
                    $sub_inventories = array_keys($input['sub_inventories']);
    
                    DB::beginTransaction();
                    try {
                        $crud = [
                            'user_id' => $user,
                            'party_name' => $party_name,
                            'party_address' => $party_address,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                        ];
                        
                        $update = Cart::where(['id' => $cart_id])->update($crud);

                        if($update){
                            CartUser::where(['cart_id' => $cart_id])->delete();

                            foreach($sub_users as $k => $v){
                                $sub_users_crud = [
                                    'cart_id' => $cart_id,
                                    'user_id' => $v,
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth()->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth()->user()->id
                                ];

                                $cart_user_id = CartUser::insertGetId($sub_users_crud);

                                if(empty($cart_user_id)){
                                    DB::rollback();
                                    return response()->json(['code' => 201, 'message' => 'Cart sub users insert error, please try again later']);
                                }
                            }

                            foreach($inventories as $k => $v){
                                $exst_inventory = CartInventory::select('id')->where(['cart_id' => $cart_id, 'inventory_id' => $v])->first();
                                
                                if(empty($exst_inventory)){
                                    $inventories_crud = [
                                        'cart_id' => $cart_id,
                                        'inventory_id' => $v,
                                        'status' => 'active',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'created_by' => auth()->user()->id,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => auth()->user()->id
                                    ];

                                    $cart_inventory_id = CartInventory::insertGetId($inventories_crud);

                                    if(empty($cart_inventory_id)){
                                        DB::rollback();
                                        return response()->json(['code' => 201, 'message' => 'Cart inventory insert error, please try again later']);
                                    }
                                }
                            }

                            foreach($sub_inventories as $k => $v){
                                $exst_sub_inventory = CartSubInventory::select('id')->where(['cart_id' => $cart_id, 'sub_inventory_id' => $v])->first();

                                if(empty($exst_sub_inventory)){
                                    $sub_inventories_crud = [
                                        'cart_id' => $cart_id,
                                        'sub_inventory_id' => $v,
                                        'status' => 'active',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'created_by' => auth()->user()->id,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => auth()->user()->id
                                    ];

                                    $cart_sub_inventory_id = CartSubInventory::insertGetId($sub_inventories_crud);

                                    if(empty($cart_sub_inventory_id)){
                                        DB::rollback();
                                        return response()->json(['code' => 201, 'message' => 'Cart sub inventory insert error, please try again later']);
                                    }
                                }
                            }

                            DB::commit();
                            return response()->json(['code' => 200, 'message' => 'Cart updated successfully']);
                        }else{
                            DB::rollback();
                            return response()->json(['code' => 201, 'message' => 'Cart update error, please try again later']);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['code' => 201, 'message' => 'Something went wrong, please try again later']);
                    }
                }
            }
        /** update */

        /** change-status */
            public function change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);

                    $data = Cart::where(['id' => $id])->first();

                    if(!empty($data)){
                        $delete = Cart::where(['id' => $id])->delete();
                        
                        if($delete){
                            return response()->json(['code' => 200]);
                        }else{
                            return response()->json(['code' => 201]);
                        }
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** change-status */

        /** users */
            public function users(Request $request){
                $cart_id = $request->cart_id;
                $user_id = '';
                
                if(!empty($cart_id)){
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
                    $users = '<option value="">Select user</option>';
                    foreach($data as $row){
                        $selected = '';
                        if(!empty($user_id) && $row->id == $user_id){ $selected = 'selected'; } 
                        $users .= "<option value='$row->id' $selected >$row->name</option>";
                    }

                    return json_encode(['code' => 200, 'data' => $users]);
                }else{
                    return json_encode(['code' => 201]);
                }
            }
        /** users */

        /** sub-users */
            public function sub_users(Request $request){
                if($request->id == '')
                    return json_encode(['code' => 201]);

                $cart_id = $request->cart_id;
                $users_id = [];

                if(!empty($cart_id)){
                    $cartUsers = CartUser::select('user_id')->where(['cart_id' => $cart_id])->get();

                    if($cartUsers->isNotEmpty()){
                        foreach($cartUsers as $row){
                            $users_id[] = $row->user_id;
                        }
                    }
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
                    $users = '';
                    foreach($data as $row){
                        $selected = '';
                        if(!empty($users_id) && in_array($row->id, $users_id)){ $selected = 'selected'; } 

                        $users .= "<option value='$row->id' $selected >$row->name</option>";
                    }

                    return json_encode(['code' => 200, 'data' => $users]);
                }else{
                    return json_encode(['code' => 201]);
                }
            }
        /** sub-users */

        /** inventories */
            public function inventories(Request $request){
                $search = $request->search;
                $selected = json_decode($request->selected);

                $cart_id = $request->cart_id;

                $collection = ItemInventory::select('items_inventories.id', 'items_inventories.title', 
                                                DB::Raw("(select COUNT(items_inventories_items.id) from items_inventories_items where items_inventories_items.item_inventory_id = items_inventories.id) as items")
                                            )
                                            ->where(['items_inventories.status' => 'active']);
                if($cart_id != ''){
                    $collection->whereNotIn('items_inventories.id', function($query) use ($cart_id) {
                                    $query->select('inventory_id')->from('cart_inventories')->where('cart_id', '!=', $cart_id)->where('status', '!=', 'inactive'); 
                                });
                }else{
                    $collection->whereNotIn('items_inventories.id', function($query) {
                                    $query->select('inventory_id')->from('cart_inventories')->where('status', '!=', 'inactive'); 
                                });
                }

                if($search != '')
                    $collection->where('items_inventories.title', 'like', '%'.$search.'%');

                $data = $collection->paginate(5);
                
                $view = view('cart.inventories_table', compact('data', 'selected'))->render();
                $pagination = view('cart.inventories_pagination', compact('data'))->render();
                
                return response()->json(['success' => true, 'data' => $view, 'pagination' => $pagination]);
            }
        /** inventories */

        /** delete-inventories */
            public function delete_inventories(Request $request){
                $id = $request->id;
                $cart_id = $request->cart_id;

                if($id == '' || $cart_id == '')
                    return response()->json(['code' => 201]);

                $exst = CartInventory::where(['cart_id' => $cart_id, 'inventory_id' => $id])->first();

                if($exst){
                    $delete = CartInventory::where(['cart_id' => $cart_id, 'inventory_id' => $id])->delete();

                    if($delete)
                        return response()->json(['code' => 200]);
                    else
                        return response()->json(['code' => 201]);
                }else{
                    return response()->json(['code' => 200]);
                }
            }
        /** delete-inventories */

        /** sub-inventories */
            public function sub_inventories(Request $request){
                $search = $request->search;
                $selected = json_decode($request->selected);
                $cart_id = $request->cart_id;
                
                $collection = SubItemInventory::select('sub_items_inventories.id', 'sub_items_inventories.title', 
                                                DB::Raw("(select COUNT(sub_items_inventories_items.id) from sub_items_inventories_items where sub_items_inventories_items.sub_item_inventory_id = sub_items_inventories.id) as items")
                                            )
                                            ->where(['sub_items_inventories.status' => 'active']);
                
                if($cart_id != ''){
                    $collection->whereNotIn('sub_items_inventories.id', function($query) use ($cart_id) {
                                    $query->select('sub_inventory_id')->from('cart_sub_inventories')->where('cart_id', '!=', $cart_id)->where('status', '!=', 'inactive'); 
                                });
                }else{
                    $collection->whereNotIn('sub_items_inventories.id', function($query) {
                                    $query->select('sub_inventory_id')->from('cart_sub_inventories')->where('status', '!=', 'inactive'); 
                                });
                }

                if($search != '')
                    $collection->where('sub_items_inventories.title', 'like', '%'.$search.'%');

                $data = $collection->paginate(5);
                
                $view = view('cart.sub_inventories_table', compact('data', 'selected'))->render();
                $pagination = view('cart.sub_inventories_pagination', compact('data'))->render();
                
                return response()->json(['success' => true, 'data' => $view, 'pagination' => $pagination]);
            }
        /** sub-inventories */

        /** delete-sub-inventories */
            public function delete_sub_inventories(Request $request){
                $id = $request->id;
                $cart_id = $request->cart_id;

                if($id == '' || $cart_id == '')
                    return response()->json(['code' => 201]);

                $exst = CartSubInventory::where(['cart_id' => $cart_id, 'sub_inventory_id' => $id])->first();

                if($exst){
                    $delete = CartSubInventory::where(['cart_id' => $cart_id, 'sub_inventory_id' => $id])->delete();

                    if($delete)
                        return response()->json(['code' => 200]);
                    else
                        return response()->json(['code' => 201]);
                }else{
                    return response()->json(['code' => 200]);
                }
            }
        /** delete-sub-inventories */
    }