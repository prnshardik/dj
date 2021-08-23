<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Models\Location;
    use Auth, DB, Validator, File;

    class LocationsController extends Controller{
        /** insert */
            public function insert(Request $request){
                $rules = [
                    'latitude' => 'required',
                    'longitude' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                    'user_id' => auth('sanctum')->user()->id,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $last_id = Location::insertGetId($crud);

                if($last_id)
                    return response()->json(['status' => 200, 'message' => 'Location inserted successfully']);
                else
                    return response()->json(['status' => 201, 'message' => 'Faild to insert location, please try again']);
            }
        /** insert */   
    }
