<?php

    namespace App\Http\Controllers\API\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\ItemCategory;
    use App\Models\Item;
    use App\Models\ItemInventory;
    use App\Models\ItemInventoryItem;
    use Auth, DB, Validator, File;

    class ItemsInventoriesController extends Controller{
        /** index */
            public function index(Request $request){
                $image_path = URL('uploads/items_inventory').'/';
                $qr_path = URL('uploads/qrcodes/items_inventory').'/';

                $data = ItemInventory::select('id', 'title', 'status',
                                                DB::Raw("CASE
                                                    WHEN ".'image'." != '' THEN CONCAT("."'".$image_path."'".", ".'image'.")
                                                    ELSE CONCAT("."'".$image_path."'".", 'default.png')
                                                END as image"),
                                                DB::Raw("CASE
                                                    WHEN ".'qrcode'." != '' THEN CONCAT("."'".$qr_path."'".", ".'qrcode'.")
                                                    ELSE ''
                                                END as qrcode"))
                                            ->orderBy('id', 'desc')
                                            ->get();

                if($data->isNotEmpty()){
                    foreach($data as $row){
                        $inventory_items = ItemInventoryItem::where(['item_inventory_id' => $row->id, 'status' => 'active'])->count();
                        $row->items = $inventory_items;
                    }
                }

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No data found']);
            }
        /** index */

        /** single */
            public function single(Request $request, $id = ''){
                if($id == '')
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);

                $generate = _generate_qrcode($id, 'item_inventory');

                if($generate){
                    $image_path = URL('uploads/items_inventory').'/';
                    $qr_path = URL('uploads/qrcodes/items_inventory').'/';

                    $data = ItemInventory::select('id', 'title', 'status',
                                                    DB::Raw("CASE
                                                        WHEN ".'image'." != '' THEN CONCAT("."'".$image_path."'".", ".'image'.")
                                                        ELSE CONCAT("."'".$image_path."'".", 'default.png')
                                                    END as image"),
                                                    DB::Raw("CASE
                                                        WHEN ".'qrcode'." != '' THEN CONCAT("."'".$qr_path."'".", ".'qrcode'.")
                                                        ELSE ''
                                                    END as qrcode"))
                                                ->where(['id' => $id])
                                                ->first();
                    
                    if($data){
                        $inventory_items = ItemInventoryItem::select('items_inventories_items.id', 'items.name', DB::Raw("SUBSTRING(".'items.description'.", 1, 30) as description"))
                                                ->leftjoin('items', 'items.id', 'items_inventories_items.item_id')
                                                ->where(['items_inventories_items.item_inventory_id' => $data->id])
                                                ->get();

                        if($inventory_items->isNotEmpty())
                            $data->items = $inventory_items;
                        else
                            $data->items = collect();

                        return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                    }else{
                        return response()->json(['status' => 201, 'message' => 'No data found']);
                    }
                }else{
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                }
            }
        /** single */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'title' => 'required',
                    'items_id' => 'required|array|min:1'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $file_to_upload = public_path().'/uploads/items_inventory/';
                if (!File::exists($file_to_upload))
                    File::makeDirectory($file_to_upload, 0777, true, true);

                $qr_to_upload = public_path().'/uploads/qrcodes/items_inventory/';
                if (!File::exists($qr_to_upload))
                    File::makeDirectory($qr_to_upload, 0777, true, true);

                $crud = [
                    'title' => ucfirst($request->title),
                    'description' => $request->description ?? NULL,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('sanctum')->user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
                ];

                if(!empty($request->file('image'))){
                    $file = $request->file('image');
                    $filenameWithExtension = $request->file('image')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filenameToStore = time()."_".$filename.'.'.$extension;

                    $crud["image"] = $filenameToStore;
                }else{
                    $crud["image"] = 'default.png';
                }

                DB::beginTransaction();
                try {
                    $last_id = ItemInventory::insertGetId($crud);

                    if($last_id){
                        $qrname = 'qrcode_'.$last_id.'.png';

                        \QrCode::size(500)->format('png')->merge('/public/qr_logo.png', .3)->generate('itemsInventories-'.$last_id, public_path('uploads/qrcodes/items_inventory/'.$qrname));

                        $update = ItemInventory::where(['id' => $last_id])->update(['qrcode' => $qrname]);

                        if($update){
                            $items_id = $request->items_id;

                            if($items_id[0] == null)
                                return response()->json(['status' => 422, 'message' => ['items_id' => 'Please select atleast one item']]);

                            for($i=0; $i<count($items_id); $i++){
                                $ivti_crud = [
                                    'item_inventory_id' => $last_id,
                                    'item_id' => $items_id[$i],
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth('sanctum')->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth('sanctum')->user()->id
                                ];

                                ItemInventoryItem::insertGetId($ivti_crud);
                            }

                            if(!empty($request->file('image')))
                                $file->move($file_to_upload, $filenameToStore);

                            DB::commit();
                            return response()->json(['status' => 200, 'message' => 'Record added successfully']);
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Faild to add record']);
                        }
                    }else{
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Faild to add record']);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 201, 'message' => 'Faild to add record']);
                }
            }
        /** insert */

        /** update */
            public function update(Request $request){
                $rules = [
                    'id' => 'required',
                    'title' => 'required',
                    'items_id' => 'required|array|min:1'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $exst_record = ItemInventory::where(['id' => $request->id])->first(); 

                $file_to_upload = public_path().'/uploads/items_inventory/';
                if (!File::exists($file_to_upload))
                    File::makeDirectory($file_to_upload, 0777, true, true);

                $crud = [
                    'title' => ucfirst($request->title),
                    'description' => $request->description ?? NULL,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth()->user()->id
                ];

                if(!empty($request->file('image'))){
                    $file = $request->file('image');
                    $filenameWithExtension = $request->file('image')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filenameToStore = time()."_".$filename.'.'.$extension;

                    $crud["image"] = $filenameToStore;
                }

                DB::beginTransaction();
                try {
                    $update = ItemInventory::where(['id' => $request->id])->update($crud);

                    if($update){
                        $items_id = $request->items_id;

                        if($items_id[0] == null)
                            return response()->json(['status' => 422, 'message' => ['items_id' => 'Please select atleast one item']]);

                        for($i=0; $i<count($items_id); $i++){
                            $exst_items = ItemInventoryItem::where(['item_inventory_id' => $request->id, 'item_id' => $items_id[$i]])->first();

                            if(empty($exst_items)){
                                $ivti_crud = [
                                    'item_inventory_id' => $request->id,
                                    'item_id' => $items_id[$i],
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth()->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth()->user()->id
                                ];

                                ItemInventoryItem::insertGetId($ivti_crud);
                            }
                        }

                        if(!empty($request->file('image')))
                            $file->move($file_to_upload, $filenameToStore);

                        DB::commit();
                        return response()->json(['status' => 200, 'message' => 'Record updated successfully']);
                    }else{
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Faild to add record']);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 201, 'message' => 'Faild to add record']);
                }
            }
        /** update */
        
        /** status-chagne */
            public function status_change(Request $request){
                $rules = [
                    'id' => 'required',
                    'status' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $data = ItemInventory::where(['id' => $request->id])->first();

                if(!empty($data)){
                    if($request->status == 'deleted')
                        $update = ItemInventory::where(['id' => $request->id])->delete();
                    else
                        $update = ItemInventory::where(['id' => $request->id])->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                    
                    if($update){
                        if($request->status == 'deleted'){
                            $file_path = public_path().'/uploads/items_inventory/'.$data->image;

                            if(File::exists($file_path) && $file_path != ''){
                                if($data->image != 'default.png')
                                    @unlink($file_path);
                            }

                            $qr_path = public_path().'/uploads/qrcodes/items_inventory/'.$data->qrcode;

                            if(File::exists($qr_path) && $qr_path != ''){
                                if($data->qrcode != 'default.png')
                                    @unlink($qr_path);
                            }
                        }

                        return response()->json(['code' => 200, 'message' =>'Status change successfully']);
                    }else{
                        return response()->json(['code' => 201, 'message' =>'Something went wrong']);
                    }
                }else{
                    return response()->json(['code' => 201, 'message' =>'Something went wrong']);
                }
            }
        /** status-chagne */

        /** items */
            public function items(Request $request){
                $inventory_id = $request->inventory_id ?? NULL;

                $inventory_items = [];
                if($inventory_id != '')
                    $inventory_items = ItemInventoryItem::select('item_id')->where(['item_inventory_id' => $inventory_id])->get()->toArray();

                if(!empty($inventory_items)){
                    $inventory_items = array_map(function($row){
                        return $row['item_id'];
                    }, $inventory_items);
                }

                $collection = Item::select('id', 'name', 'description')
                                    ->where(['status' => 'active']);

                if($inventory_id != ''){
                    $collection->whereNotIn('id', function($query) use ($inventory_id) {
                        $query->select('item_id')->from('items_inventories_items')->where(['status' => 'active'])->where('item_inventory_id', '!=', $inventory_id); 
                    });
                }else{                
                    $collection->whereNotIn('id', function($query) {
                        $query->select('item_id')->from('items_inventories_items')->where(['status' => 'active']); 
                    });
                }

                $data = $collection->get();

                if($data->isNotEmpty()){
                    foreach($data as $row){
                        $row->selected = false;

                        if(!empty($inventory_items)){
                            if(in_array($row->id, $inventory_items)){
                                $row->selected = true;
                            }
                        }
                    }
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                }else{
                    return response()->json(['status' => 201, 'message' => 'No data found']);                
                }
            }
        /** items */

        /** items-delete */
            public function items_delete(Request $request){
                $rules = [
                    'id' => 'required',
                    'inventory_id' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $exst = ItemInventoryItem::where(['item_id' => $request->id, 'item_inventory_id' => $request->inventory_id])->first();

                if($exst){
                    $delete = ItemInventoryItem::where(['id' => $exst->id])->delete();

                    if($delete)
                        return response()->json(['status' => 200, 'message' => 'Record deleted successfully']);
                    else
                        return response()->json(['status' => 201, 'message' => 'Failed to deleted record']);
                }else{
                    return response()->json(['status' => 200, 'message' => 'Record deleted successfully']);
                }
            }
        /** items-delete */
    }