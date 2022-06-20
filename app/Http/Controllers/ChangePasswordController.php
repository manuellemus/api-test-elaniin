<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdatePasswordRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller {

    public function passwordResetProcess(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'passwordToken' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        if ($this->verifyToken($request)) {
            return $this->resetPassword($request);
        }
        else{
            return response()->json([
            'error' => 'The token has expired'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    
    // Verify if token is valid
    private function verifyToken($request){

        $data = DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->passwordToken
        ])->first();

        if (!empty($data)) {
            $dateTime = Carbon::now()->format('Y-m-d H:i:s');
            $created = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->addMinutes(15);
            if ($dateTime < $created){
                return true;
            } 
        }
        return false;
    }
    
    // Reset password
    private function resetPassword($request) {

        // update password
        $userData = User::whereEmail($request->email)->first();
        $userData->update([
        'password'=>bcrypt($request->password)
        ]);
        
        // remove verification data from db
        DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->passwordToken
        ])->delete();

        return response()->json([
        'data'=>'Password has been updated.',
        ],Response::HTTP_CREATED); 
    }    
}