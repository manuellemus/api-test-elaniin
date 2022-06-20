<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\SendMail;
use App\Models\User;
use Carbon\Carbon;

class PasswordResetRequestController extends Controller
{


    /**
     * @OA\Post(
     *  path="/api/reset-password-request",
     *  summary="send token",
     *  tags={"Reset password"},
     *      @OA\Parameter(
     *         description="Enter email fro send a token",
     *         name="email",
     *         in="query",
     *         description="email",
     *         required = true
     *     ),
     *      @OA\Response(
     *         response=200, 
     *             description="Check your inbox, we have sent a link to reset email."
     *      ),
     *     @OA\Response(
     *         response=500,
     *         description="Email does not exist.."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Email does not exist.."
     *     ),
     * )
     */

    public function sendPasswordResetEmail(Request $request)
    {
        // If email does not exist
        if (!$this->validEmail($request->email)) {
            return response()->json([
                'status' => 500,
                'message' => 'Email does not exist.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            // If email exists
            $this->sendMail($request->email);
            return response()->json([
                'status' => 200,
                'message' => 'Check your inbox, we have sent a link to reset email.'
            ], Response::HTTP_OK);
        }
    }

    public function sendMail($email)
    {
        $token = $this->generateToken($email);
        Mail::to($email)->send(new SendMail($token));
    }

    public function validEmail($email)
    {
        return !!User::where('email', $email)->first();
    }

    public function generateToken($email)
    {
        //   $isOtherToken = DB::table('password_resets')->where('email', $email)->first();
        //   if($isOtherToken) {
        //     return $isOtherToken->token;
        //   }
        $token = Str::random(80);;
        $this->storeToken($token, $email);
        return $token;
    }

    public function storeToken($token, $email)
    {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
