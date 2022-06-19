<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }
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

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'birthDate' => 'required|string|max:10',
            'telephone' => 'required|numeric|min:8'
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

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $user = User::find($id);

            $user->delete();

            DB::commit();

            return response()->json([
                'status' => 204,
                'message' => 'data deleted successfully',
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
