<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AddressController extends Controller {

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request                         $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create( Request $request ) {

        $data = $request->validate( [
            'address' => 'required|string',
        ] );

        try {
            if ( empty( $data['user_id'] ) ) {
                $data['user_id'] = auth()->user()->id;
            }

            $address = Address::create( $data );
        } catch ( \Throwable $e ) {
            return response( $e, 500 );
        }

        /*
         * Log Address
         */
        UserLog::create( ['user_id' => auth()->user()->id, 'action' => 'create_address', 'userlog_previous_id' => UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', auth()->user()->id )->first() ? UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', auth()->user()->id )->first()->id : null] );

        return response()->json( ['message' => 'Address has been attached to user', 'address' => $address], 200 );
    }

    /**
     *
     * @param  Request                         $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( Request $request, $address_id ) {

        $data = $request->validate( [
            'address' => 'required|string',
        ] );

        $address = Address::findOrFail( $address_id );

        if ( $address ) {
            $address->update( $data );
        }

        /*
         * Log Address
         */
        UserLog::create( ['user_id' => auth()->user()->id, 'action' => 'update_address', 'userlog_previous_id' => UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', auth()->user()->id )->first() ? UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', auth()->user()->id )->first()->id : null] );
        return response()->json( ['message' => 'Address has been updated', 'address' => $address], 200 );
    }

    /*
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_all() {
        $user = auth()->user();
        return Address::orderBy( 'created_at', 'desc' )->where( 'user_id', $user->id )->where( 'deleted_at', null )->get();
    }

    /*
     *
     * @param  Request                         $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get( $address_id ) {
        $address          = Address::findOrFail( $address_id );
        $address['users'] = $address->user;
        return $address;
    }

    /*
     * Delete Address
     */
    public function delete( $address_id ) {

        $address = Address::findOrFail( $address_id );

        if ( $address ) {
            $address->deleted_at = Carbon::now();
            $address->save();

            /*
             * Log Address
             */
            UserLog::create( ['user_id' => auth()->user()->id, 'action' => 'delete_address', 'userlog_previous_id' => UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', auth()->user()->id )->first() ? UserLog::orderBy( 'created_at', 'desc' )->where( 'user_id', auth()->user()->id )->first()->id : null] );

            return response()->json( ['message' => 'Address has been deleted', 'address' => $address], 200 );
        }
    }

}
