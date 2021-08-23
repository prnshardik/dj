<?php

    namespace App\Http\Controllers\API\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\User;
    use Auth, DB, Validator, File;

    class UsersController extends Controller{
        /** index */
            public function index(Request $request){
                $path = URL('/uploads/users').'/';
                $data = User::select('id', 'name', 'phone', 'email', 'is_admin', 'status', 'device_id',
                                    DB::Raw("CASE
                                        WHEN ".'image'." != '' THEN CONCAT("."'".$path."'".", ".'image'.")
                                        ELSE CONCAT("."'".$path."'".", 'default.png')
                                    END as image")
                                )
                                ->where(['status' => 'active'])
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

                $path = URL('/uploads/users').'/';
                $data = User::select('id', 'name', 'phone', 'email', 'is_admin', 'status', 'device_id',
                                    DB::Raw("CASE
                                        WHEN ".'image'." != '' THEN CONCAT("."'".$path."'".", ".'image'.")
                                        ELSE CONCAT("."'".$path."'".", 'default.png')
                                    END as image")
                                )
                            ->where(['id' => $id])
                            ->first();

                if($data)
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No data found']);                        
            }
        /** single */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'phone' => 'required|numeric|unique:users,phone',
                    'password' => 'required|min:7'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $folder_to_upload = public_path().'/uploads/users/';
                
                $crud = [
                    'name' => ucfirt($request->name),
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => bcrypt($request->password),
                    'is_admin' => 'n',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('sanctum')->user()->id,
                    'updated_by' => auth('sanctum')->user()->id
                ];

                if(!empty($request->file('image'))){
                    $file = $request->file('image');
                    $filenameWithExtension = $request->file('image')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filenameToStore = time()."_".$filename.'.'.$extension;

                    if (!File::exists($folder_to_upload))
                        File::makeDirectory($folder_to_upload, 0777, true, true);

                    $crud["image"] = $filenameToStore;
                }else{
                    $crud["image"] = 'default.png';
                }

                $last_id = User::insertGetId($crud);

                if($last_id){
                    if(!empty($request->file('image')))
                        $file->move($folder_to_upload, $filenameToStore);
                    
                    return response()->json(['status' => 200, 'message' => 'Record added successfully']);
                }else{
                    return response()->json(['status' => 201, 'message' => 'Faild to add record']);
                }
            }
        /** insert */

        /** update */
            public function update(Request $request){
                $rules = [
                    'id' => 'required',
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,'.$request->id,
                    'phone' => 'required|numeric|unique:users,phone,'.$request->id
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $exst_record = User::where(['id' => $request->id])->first(); 
                $folder_to_upload = public_path().'/uploads/users/';

                $crud = [
                    'name' => ucfirst($request->name),
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
                ];

                if(!empty($request->file('image'))){
                    $file = $request->file('image');
                    $filenameWithExtension = $request->file('image')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filenameToStore = time()."_".$filename.'.'.$extension;

                    if (!File::exists($folder_to_upload))
                        File::makeDirectory($folder_to_upload, 0777, true, true);

                    $crud["image"] = $filenameToStore;
                }else{
                    $crud["image"] = $exst_record->image;
                }

                $update = User::where(['id' => $request->id])->update($crud);

                if($update){
                    if(!empty($request->file('image')))
                        $file->move($folder_to_upload, $filenameToStore);

                    if($exst_record->image != null || $exst_record->image != ''){
                        $file_path = public_path().'/uploads/users/'.$exst_record->image;

                        if(File::exists($file_path) && $file_path != ''){
                            if($exst_record->image != 'default.png')
                                @unlink($file_path);
                        }
                    }
                    return response()->json(['status' => 200, 'message' => 'User updated successfully']);
                }else{
                    return response()->json(['status' => 201, 'message' => 'Faild to update user']);
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

                $data = User::where(['id' => $request->id])->first();

                if(!empty($data)){
                    if($request->status == 'deleted'){
                        $update = User::where(['id' => $request->id])->delete();

                        if($update){
                            $file_path = public_path().'/uploads/users/'.$data->image;
                            if(File::exists($file_path) && $file_path != ''){
                                if($data->image != 'default.png')
                                    @unlink($file_path);
                            }

                            return response()->json(['code' => 200, 'message' =>'Record deleted successfully']);
                        }else{
                            return response()->json(['code' => 201, 'message' =>'Faild to delete record']);
                        }
                    }else{
                        $update = User::where(['id' => $request->id])->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                    }
                    
                    if($update)
                        return response()->json(['code' => 200, 'message' =>'Status change successfully']);
                    else
                        return response()->json(['code' => 201, 'message' =>'Faild to change status']);
                }else{
                    return response()->json(['code' => 201, 'message' =>'No record found']);
                }
            }
        /** change-status */
    }