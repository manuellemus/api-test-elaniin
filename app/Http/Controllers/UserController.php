<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/** 
 * @OA\Info(title="API Elaniin", version="1.0")
 * 
 * @OA\Server(url="http://localhost:8000")
 */

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }
    /**
     * @OA\Get(
     *  path="/api/users/",
     *  summary="Get the list of users",
     *  tags={"users End points"},
     *      @OA\Response(
     *         response=200, 
     *             description="Return a list of users"
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

    public function index()
    {
        try {
            $user = User::paginate();
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'response' => $user,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ups, fail request',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }
    /**
     * @OA\Get(
     *  path="/api/users/11",
     *  summary="Get One user by id",
     *  tags={"users End points"},
     *      @OA\Response(
     *         response=200, 
     *             description="Return one user"
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
    public function get_user($id)
    {
        try {
            $user = User::find($id);
            return response()->json([
                'status' => 200,
                'message' => 'Data found successfully',
                'response' => $user,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/users/search",
     *  summary="search user by User Name, Name and Email",
     *  tags={"users End points"},
     *      @OA\Parameter(
     *         description="Enter Name",
     *         name="name",
     *         in="query",
     *         description="name",
     *         example="admin",
     *     ),
     *      @OA\Parameter(
     *         description="Enter de Email",
     *         name="email",
     *         in="query",
     *         description="Email",
     *         example="admin@admin.com",
     *     ),
     *      @OA\Parameter(
     *         description="Enter User Name",
     *         name="userName",
     *         in="query",
     *         description="UserName",
     *     ),
     *      @OA\Response(
     *         response=200, 
     *             description="Return one user"
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
    public function search(Request $request)
    {
        try {
            $user = User::select()
                ->orderByDesc('id');

            if (!empty($request->name)) {
                $user->NAME($request->name);
            }
            if (!empty($request->userName)) {
                $user->USERNAME($request->userName);
            }

            if (!empty($request->email)) {
                $user->EMAIL($request->email);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Data found',
                'data' => $user->get()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/users/store",
     *  summary="Save user",
     *  tags={"users End points"},
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

    public function store(Request $request)
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

        try {
            DB::beginTransaction();
            $user = new User;
            $user_array = [
                'name' => $request->name,
                'email' => $request->email,
                'userName' => $request->userName,
                'birthDate' => $request->birthDate,
                'telephone' => $request->telephone,
                'password' => Hash::make($request->password),
            ];
            $user->fill($user_array);
            $user->save();

            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Data save successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }

        /**
     * @OA\Put(
     *  path="/api/users/update/9",
     *  summary="Update user",
     *  tags={"users End points"},
     *      @OA\Parameter(
     *         description="Enter Name",
     *         name="name",
     *         in="query",
     *         description="name",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter User birthDate",
     *         name="birthDate",
     *         in="query",
     *         description="birthDate",
     *         required = true
     *     ),
     *      @OA\Parameter(
     *         description="Enter telephone",
     *         name="telephone",
     *         in="query",
     *         description="telephone",
     *         required = true
     *     ),
     *      @OA\Response(
     *         response=200, 
     *          description="Data modify successfully"
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

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'birthDate' => 'required|string|max:10',
            'telephone' => 'required|numeric|min:8|max:11'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        try {
            DB::beginTransaction();
            $user = User::find($id);
            $user_array = [
                'name' => $request->name,
                'birthDate' => $request->birthDate,
                'telephone' => $request->telephone,
            ];
            $user->fill($user_array);
            $user->save();

            DB::commit();

            return response()->json([
                'status' => 204,
                'message' => 'Data modify successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }
    /**
     * @OA\Delete(
     *  path="/api/users/7",
     *  summary="Soft Delete One user by id",
     *  tags={"users End points"},
     *      @OA\Response(
     *         response=204, 
     *             description="Data deleted successfully"
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
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $user = User::find($id);

            $user->delete();

            DB::commit();

            return response()->json([
                'status' => 204,
                'message' => 'Data deleted successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Oops something went wrong',
                'details' => $e->getFile() . $e->getLine() . $e->getMessage()
            ]);
        }
    }
}
