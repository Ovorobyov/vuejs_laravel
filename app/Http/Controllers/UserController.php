<?php

namespace App\Http\Controllers;

use Validator;
use http\Env\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\User;

class UserController extends Controller
{
    public function register(Request $request){
        // Validate
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);


        // Create New User
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user = $user->save();

        try {
            if ($user) {
                // Response data
                $credential = $request->only('email','password');

                if($token = JWTAuth::attempt($credential)){
                    // Return error
                    return response()->json(["token" => $token], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    public function login(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 200);
        }

        $credential = $request->only('email','password');

        try {
            if (!$token = JWTAuth::attempt($credential)) {
                // Return error
                return response()->json(['message' => 'Login failed'], 200);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['message' => 'token_expired'], $e->getStatusCode());

        }

        // Response Auth Token
        return response()->json(["token" => $token], 200);

    }

    public function check(Request $request){
        return JWTAuth::parseToken()->authenticate();
    }

}
