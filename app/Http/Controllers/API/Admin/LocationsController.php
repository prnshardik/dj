<?php

    namespace App\Http\Controllers\API\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Models\Location;
    use Auth, DB, Validator, File;

    class LocationsController extends Controller{
        public function locations(Request $request){
            $rules = ['user_id' => 'required'];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails())
                return response()->json(['status' => 422, 'message' => $validator->errors()]);

            $data = Location::select('id', 'latitude', 'longitude')->where(['user_id' => $request->user_id])->get();

            if($data->isNotEmpty())
                return response()->json(['status' => 200, 'message' => 'Records found', 'data' => $data]);
            else
                return response()->json(['status' => 201, 'message' => 'No records found']);
        }

        public function location(Request $request){
            $rules = ['id' => 'required'];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails())
                return response()->json(['status' => 422, 'message' => $validator->errors()]);

            $data = Location::select('id', 'latitude', 'longitude')->where(['id' => $request->id])->first();

            if(!empty($data))
                return response()->json(['status' => 200, 'message' => 'Record found', 'data' => $data]);
            else
                return response()->json(['status' => 201, 'message' => 'No record found']);
        }

        public function last_location(Request $request){
            $rules = ['user_id' => 'required'];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails())
                return response()->json(['status' => 422, 'message' => $validator->errors()]);

            $data = Location::select('id', 'latitude', 'longitude')->where(['user_id' => $request->user_id])->orderby('id', 'desc')->first();

            if(!empty($data))
                return response()->json(['status' => 200, 'message' => 'Record found', 'data' => $data]);
            else
                return response()->json(['status' => 201, 'message' => 'No record found']);
        }
    }