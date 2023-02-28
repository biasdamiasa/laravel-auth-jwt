<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if(!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid email or password'
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Generate token failed'
            ]);
        }

        $user = JWTAuth::user();

        return response()->json([
            'message' => 'Berhasil login',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function getUser()
    {
        $user = JWTAuth::user();
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;

        $user->save();

        $token = JWTAuth::fromUser($user);

        $data = User::where('email', '=', $request->email)->first();
        return response()->json([
            'message' => 'Berhasil tambah data baru',
            'data' => $data
        ]);
    }

    public function loginCheck(){
		try {
			if(!$user = JWTAuth::parseToken()->authenticate()){
				return response()->json([
					'success' => false,
					'message' => 'Invalid Token'
				]);
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
			return response()->json([
				'success' => false,
				'message' => 'Token expired!'
			]);
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
			return response()->json([
				'success' => false,
				'message' => 'Invalid Token!'
			]);
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e){
			return response()->json([
				'success' => false,
				'message' => 'Token Absent'
			]);
		}

		return response()->json([
			'success' => true,
			'message' => 'Success'
		]);
	}
	
    public function logout(Request $request)
    {
		if(JWTAuth::invalidate(JWTAuth::getToken())) {
			return response()->json(['message' => 'You are logged out']);
        } else {
            return response()->json(['message' => 'Failed']);
        }
    }
}
