<?php

    namespace App\Http\Controllers\API\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\SubItemCategory;
    use App\Models\SubItem;
    use Auth, DB, Validator, File;

    class SubItemController extends Controller{
        /** index */
            public function index(Request $request){
                $image = URL('/uploads/sub_items').'/';
                $qrcode = URL('/uploads/qrcodes/sub_items').'/';

                $data = SubItem::select('sub_items.id', 'sub_items.name', 'sub_items.description', 'sub_items.status', 
                                        DB::Raw("CASE
                                            WHEN ".'sub_items.image'." != '' THEN CONCAT("."'".$image."'".", ".'sub_items.image'.")
                                            ELSE CONCAT("."'".$image."'".", 'default.png')
                                        END as image"),
                                        DB::Raw("CASE
                                            WHEN ".'sub_items.qrcode'." != '' THEN CONCAT("."'".$qrcode."'".", ".'sub_items.qrcode'.")
                                            ELSE ''
                                        END as qrcode"),
                                        'sub_items_categories.id as category_id', 'sub_items_categories.title as category_title'
                                    )
                                ->leftjoin('sub_items_categories', 'sub_items_categories.id', 'sub_items.category_id')
                                ->get();

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

                $image = URL('/uploads/sub_items').'/';
                $qrcode = URL('/uploads/qrcodes/sub_items').'/';

                $data = SubItem::select('sub_items.id', 'sub_items.name', 'sub_items.description', 'sub_items.status', 
                                        DB::Raw("CASE
                                            WHEN ".'sub_items.image'." != '' THEN CONCAT("."'".$image."'".", ".'sub_items.image'.")
                                            ELSE CONCAT("."'".$image."'".", 'default.png')
                                        END as image"),
                                        DB::Raw("CASE
                                            WHEN ".'sub_items.qrcode'." != '' THEN CONCAT("."'".$qrcode."'".", ".'sub_items.qrcode'.")
                                            ELSE ''
                                        END as qrcode"),
                                        'sub_items_categories.id as category_id', 'sub_items_categories.title as category_title'
                                    )
                                ->leftjoin('sub_items_categories', 'sub_items_categories.id', 'sub_items.category_id')
                                ->where(['sub_items.id' => $id])
                                ->first();

                if($data)
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No data found']);                        
            }
        /** single */

        /** insert */
            public function insert(Request $request){
                $rules = ['category_id' => 'required', 'name' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $file_to_uploads = public_path().'/uploads/sub_items/';
                if (!File::exists($file_to_uploads))
                    File::makeDirectory($file_to_uploads, 0777, true, true);

                $qr_to_uploads = public_path().'/uploads/qrcodes/sub_items/';
                if (!File::exists($qr_to_uploads))
                    File::makeDirectory($qr_to_uploads, 0777, true, true);
                
                DB::beginTransaction();
                try {
                    $names = [];
                    $qrnames = [];
                    $quantity = $request->quantity ?? 1;
                    $i = 0;

                    while($i < $quantity){
                        $crud = [
                            'category_id' => $request->category_id,
                            'name' => ucfirst($request->name),
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

                            array_push($names, $filenameToStore);
                        }else{
                            $crud["image"] = 'default.png';
                        }

                        $last_id = SubItem::insertGetId($crud);

                        if($last_id){
                            $qrname = 'qrcode_'.$last_id.'.png';
                            array_push($qrnames, $qrname);

                            \QrCode::size(500)->format('png')->merge('/public/qr_logo.png', .3)->generate('sub_items-'.$last_id, public_path('uploads/qrcodes/sub_items/'.$qrname));

                            $update = SubItem::where(['id' => $last_id])->update(['qrcode' => $qrname]);

                            if($update){
                                $i++;
                                if(!empty($request->file('image')))
                                    File::copy($request->file('image'), public_path('/uploads/sub_items'.'/'.$filenameToStore));
                            }                                
                        }
                    }

                    if($i == $quantity){
                        DB::commit();
                        return response()->json(['status' => 200, 'message' => 'Record added successfully']);
                    }else{
                        if(!empty($names)){
                            foreach($names as $name){
                                @unlink(public_path().'/uploads/sub_items/'.$name);
                            }
                        }

                        if(!empty($qrnames)){
                            foreach($qrnames as $name){
                                @unlink(public_path().'/uploads/qrcodes/sub_items/'.$name);
                            }
                        }

                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Faild to add record\'s qrcode']);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 201, 'message' => 'Faild to add record']);
                }
            }
        /** insert */

        /** update */
            public function update(Request $request){
                $rules = ['id' => 'required', 'category_id' => 'required', 'name' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $file_to_uploads = public_path().'/uploads/sub_items/';
                if (!File::exists($file_to_uploads))
                    File::makeDirectory($file_to_uploads, 0777, true, true);

                $exst_record = SubItem::where(['id' => $request->id])->first(); 

                $crud = [
                    'category_id' => $request->category_id,
                    'name' => ucfirst($request->name),
                    'description' => $request->description ?? NULL,
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
                    $crud["image"] = $exst_record->image;
                }

                $update = SubItem::where(['id' => $request->id])->update($crud);

                if($update){
                    if(!empty($request->file('image')))
                        $file->move($file_to_uploads, $filenameToStore);

                    if($exst_record->image != null || $exst_record->image != ''){
                        $file_path = public_path().'/uploads/sub_items/'.$exst_record->image;

                        if(File::exists($file_path) && $file_path != ''){
                            if($exst_record->image != 'default.png')
                                @unlink($file_path);
                        }
                    }

                    return response()->json(['status' => 200, 'message' => 'Record updated successfully']);
                }else{
                    return response()->json(['status' => 201, 'message' => 'Faild to add record']);
                }
            }
        /** update */

        /** change-status */
            public function status_change(Request $request){
                $rules = ['id' => 'required', 'status' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $data = SubItem::where(['id' => $request->id])->first();

                if(!empty($data)){
                    if($request->status == 'deleted')
                        $update = SubItem::where(['id' => $request->id])->delete();
                    else
                        $update = SubItem::where(['id' => $request->id])->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                    
                    if($update){
                        if($request->status == 'deleted'){
                            $file_path = public_path().'/uploads/sub_items/'.$data->image;

                            if(File::exists($file_path) && $file_path != ''){
                                if($data->image != 'default.png')
                                    @unlink($file_path);
                            }

                            $qr_path = public_path().'/uploads/qrcodes/sub_items/'.$data->qrcode;

                            if(File::exists($qr_path) && $qr_path != ''){
                                if($data->qrcode != 'default.png')
                                    @unlink($qr_path);
                            }
                        }

                        return response()->json(['code' => 201, 'message' => 'Record updated successfully']);
                    }else{
                        return response()->json(['code' => 201, 'message' => 'Something went wrong']);
                    }
                }else{
                    return response()->json(['code' => 201, 'message' => 'No record found']);
                }
            }
        /** change-status */
    }