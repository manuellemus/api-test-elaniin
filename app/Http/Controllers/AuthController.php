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
        $this->middleware('jwt.verify', ['except' => ['login', 'register']]);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Log in",
     *     tags={"Log in End points"},
     *      @OA\Parameter(
     *         description="email for log in is necesary",
     *         name="email",
     *         in="query",
     *         description="Email",
     *         example="admin@admin.com",
     *     ),
     *      @OA\Parameter(
     *         description="password for log in is necesary",
     *         name="password",
     *         in="query",
     *         @OA\Schema(type="string"),
     *         description="Password",
     *         example="12345678",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     )
     * )
     */

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

    /**
     * @OA\Post(
     *  path="/api/register",
     *  summary="Register new user",
     *  tags={"Log in End points"},
     *      @OA\Parameter(
     *         description="Enter Name",
     *         name="name",
     *         in="query",
     *         description="name",
     *         example="admin",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter User Name",
     *         name="userName",
     *         in="query",
     *         description="UserName",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter de Email",
     *         name="email",
     *         in="query",
     *         description="Email",
     *         example="test@gmail.com",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter User birthDate",
     *         name="birthDate",
     *         in="query",
     *         description="birthDate",
     *         example="2062-02-26",
     *     ),
     *      @OA\Parameter(
     *         description="Enter telephone",
     *         name="telephone",
     *         in="query",
     *         description="telephone",
     *         example="77553344",
     *     ),
     *      @OA\Parameter(
     *         description="Enter password",
     *         name="password",
     *         in="query",
     *         description="password",
     *         example="12345678",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter password_confirmation",
     *         name="password_confirmation",
     *         in="query",
     *         description="password_confirmation",
     *         example="12345678",
     *         required = true
     *     ),
     *      @OA\Response(
     *         response=200, 
     *             description="Data save successfully"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Oops something went wrong."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Authorization Token not found."
     *     ),
     *    security={{ "apiAuth": {} }},
     * )
     */

    public function register(Request $request)
    {

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

    /**
     * @OA\Post(
     *  path="/api/logout/",
     *  summary="destroy token",
     *  tags={"Log in End points"},
     *      @OA\Response(
     *         response=200, 
     *             description="Successfully logged out"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ups, fail request."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Authorization Token not found."
     *     ),
     *    security={{ "apiAuth": {} }},
     * )
     */
    public function logout()
    {
        auth()->logout(true);
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Post(
     *  path="/api/refresh/",
     *  summary="refresh token",
     *  tags={"Log in End points"},
     *      @OA\Response(
     *         response=200, 
     *             description="Return refresh token"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ups, fail request."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Authorization Token not found."
     *     ),
     *    security={{ "apiAuth": {} }},
     * )
     */
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
        } else {
            return $this->respond([
                'status' => 'error',
                'message' => 'Unauthorized',
                'statusCode' => Res::HTTP_UNAUTHORIZED,
            ]);
        }
    }

        /**
     * @OA\Get(
     *  path="/api/user/",
     *  summary="return user authenticated ",
     *  tags={"Log in End points"},
     *      @OA\Response(
     *         response=200, 
     *             description="Return user authenticated"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ups, fail request."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Authorization Token not found."
     *     ),
     *    security={{ "apiAuth": {} }},
     * )
     */
    public function getAuthenticatedUser()
    {
        return response()->json(auth()->user());
    }
}
