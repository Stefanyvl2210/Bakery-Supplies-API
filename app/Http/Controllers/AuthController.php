<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {

    public function login( Request $request ) {
        $fields = $request->validate( [
            'email'    => 'required|string',
            'password' => 'required|string',
        ] );

        //Check email
        $user = User::where( 'email', $fields['email'] )->first();

        //Check password
        if ( !$user || !Hash::check( $fields['password'], $user->password ) ) {
            return response()->json( [
                'Error' => 'Bad Credentials',
            ], 401 );
        }
        $user->main_role = $user->getRoleNames()[0];
        $token           = $user->createToken( 'myapptoken' )->plainTextToken;

        /*
         * Log login
         */
        UserLog::create( ['user_id' => $user->id, 'action' => 'logged_in', 'userlog_previous_id' => UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', $user->id )->first() ? UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', $user->id )->first()->id : null] );

        return response()->json( [
            'data' => [
                'user'  => $user,
                'token' => $token,
            ],
        ], 201 );
    }

    public function logout( Request $request ) {
        $user = auth()->user();
        auth()->user()->tokens()->delete();

        /*
         * Log Log out
         */
        UserLog::create( ['user_id' => $user->id, 'action' => 'verify_email', 'userlog_previous_id' => UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', $user->id )->first() ? UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', $user->id )->first()->id : null] );

        return response()->json( [
            'data' => [
                'message' => 'Logged out',
            ],
        ], 200 );
    }

    public function verify_email( Request $request ) {
        $code = $request->get( 'code' );
        $user = auth()->user();

        if ( !Hash::check( $code, $user->verification_code ) ) {
            return response()->json( ['Error' => 'Invalid Code'], 401 );
        }

        $user->email_verified_at = Carbon::now();
        $user->verification_code = null;
        $user->save();

        /*
         * Log Verify
         */
        UserLog::create( ['user_id' => $user->id, 'action' => 'verify_email', 'userlog_previous_id' => UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', $user->id )->first() ? UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', $user->id )->first()->id : null] );

        return response()->json( [
            'data' => [
                'message' => 'User Verified',
            ],
        ], 200 );
    }
}
