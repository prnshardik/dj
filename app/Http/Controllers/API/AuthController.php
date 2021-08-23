<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\User;
    use Auth, DB, Validator, File;

    class AuthController extends Controller{
        
        /** login */
            public function login(Request $request){
                $rules = [
                            'email' => 'required',
                            'password' => 'required',
                            'device_id' => 'required'
                        ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
                
                $auth = (auth()->attempt(['email' => $request->email, 'password' => $request->password, 'status' => 'active']) || auth()->attempt(['phone' => $request->email, 'password' => $request->password, 'status' => 'active']));

                if(!$auth){
                    return response()->json(['status' => 401, 'message' => 'Invalid login details']);
                }else{
                    $user = auth('sanctum')->user();

                    User::where(['id' => auth('sanctum')->user()->id])->update(['device_id' => $request->device_id]);

                    $is_admin = false;

                    if($user->is_admin == 'y')
                        $is_admin = true;   
                    
                    $token = $user->createToken('auth_token')->plainTextToken;

                    return response()->json(['status' => 200, 'message' => 'Login Successfully', 'token_type' => 'Bearer', 'access_token' => $token, 'is_admin' => $is_admin]);
                }
            }
        /** login */

        /** logout */
            public function logout(Request $request){
                $request->user()->currentAccessToken()->delete();

                return response()->json(['status' => 200, 'message' => 'Logout Successfully']);
            }
        /** logout */
    }
