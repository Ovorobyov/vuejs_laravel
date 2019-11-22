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
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ],
            [
                'name.required'              => 'Поле Имя обязательно к заполнению!',
                'email.required'             => 'Поле E-mail обязательно к заполнению!',
                'password.required'          => 'Поле Пароль обязательно к заполнению!',
                'password.confirmed'         => 'Поле Повторите пароль не совпадает с полем Пароль!',
                'password.min'               => 'Пароль должен содержать не менее 6 символов!',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 200);
        }
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
        $validator = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ],
            [
                'email.required'            => 'Поле E-mail обязательно к заполнению!',
                'email.email'               => 'Поле E-mail введено не корректно!',
                'password.required'         => 'Поле Пароль обязательно к заполнению!',
                'password.min'              => 'Пароль должен содержать не менее 6 символов!',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['messages' => $validator->errors()], 200);
        }

        $credential = $request->only('email','password');

        try {
            if (!$token = JWTAuth::attempt($credential)) {
                // Return error
                return response()->json(['messages' => ["auth"=>["Неверный логин или пароль!"]]], 200);
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
