<?php

namespace App\Http\Controllers;

use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response as Res;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Validator;
use Response;
use JWTAuth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);

    }

    public function register(Request $request){
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'userName' => 'required|string|max:30|unique:users',
            'birthDate' => 'required|string|max:10',
            'telephone' => 'required|numeric|min:8'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'userName' => $request->userName,
            'birthDate' => $request->birthDate,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        auth()->logout(true);
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        if (Auth::user()) {
            return response()->json([
                'status' => 'success',
                'user' => Auth::user(),
                'authorisation' => [
                    'token' => Auth::refresh(),
                    'type' => 'bearer',
                ]
            ]);
        } 
        else {
            return $this->respond([
                'status' => 'error',
                'message' => 'Unauthorized',
                'statusCode' => Res::HTTP_UNAUTHORIZED,
            ]);
        }
    }

    public function getAuthenticatedUser()
    {
        return response()->json(auth()->user());
    }

}