<?php

    namespace App\Http\Controllers\API\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\ItemCategory;
    use Auth, DB, Validator, File;

    class ItemsCategoriesController extends Controller{
        /** index */
            public function index(Request $request){
                $data = ItemCategory::select('id', 'title', 'description', 'status')->get();

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

                $data = ItemCategory::select('id', 'title', 'description', 'status')->where(['id' => $id])->first();

                if($data)
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No data found']);                        
            }
        /** single */

        /** insert */
            public function insert(Request $request){
                $rules = ['title' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
  
                $crud = [
                    'title' => ucfirst($request->title),
                    'description' => $request->description ?? NULL,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('sanctum')->user()->id,
                    'updated_by' => auth('sanctum')->user()->id
                ];

                $last_id = ItemCategory::insertGetId($crud);

                if($last_id)
                    return response()->json(['status' => 200, 'message' => 'Record added successfully']);
                else
                    return response()->json(['status' => 201, 'message' => 'Faild to add record']);
            }
        /** insert */

        /** update */
            public function update(Request $request){
                $rules = ['id' => 'required', 'title' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                    'title' => ucfirst($request->title),
                    'description' => $request->description ?? NULL,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
                ];

                $update = ItemCategory::where(['id' => $request->id])->update($crud);

                if($update)
                    return response()->json(['status' => 200, 'message' => 'Record updated successfully']);
                else
                    return response()->json(['status' => 201, 'message' => 'Faild to update record']);
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

                $data = ItemCategory::where(['id' => $request->id])->first();

                if(!empty($data)){
                    if($request->status == 'deleted')
                        $update = ItemCategory::where(['id' => $request->id])->delete();
                    else
                        $update = ItemCategory::where(['id' => $request->id])->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                    
                    if($update)
                        return response()->json(['code' => 200 , 'message' => 'Status change successfully']);
                    else
                        return response()->json(['code' => 201 , 'message' => 'Faild to change status']);
                }else{
                    return response()->json(['code' => 201, 'message' => 'No record found']);
                }
            }
        /** change-status */
    }