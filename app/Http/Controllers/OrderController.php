<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller {

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request                         $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create( Request $request ) {

        $data = $request->validate( [
            'address'       => 'required|string',
            'delivery_time' => 'required',
            'taxes'         => 'required',
            'total'         => 'required',

        ] );

        try {
            if ( !$data['user_id'] ) {
                $data['user_id'] = auth()->user()->id;
            }

            $address = Order::create( $data );
        } catch ( \Throwable $e ) {
            return response( $e, 500 );
        }

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

        $address = Order::findOrFail( $address_id );

        if ( $address ) {
            $address->update( $data );
        }

        return response()->json( ['message' => 'Address has been updated', 'address' => $address], 200 );
    }

    /*
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_all() {
        $user = auth()->user();
        return Order::orderBy( 'created_at', 'desc' )->where( 'user_id', $user->id )->where( 'deleted_at', false )->get();
    }

    /*
     *
     * @param  Request                         $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get( $address_id ) {
        $address          = Order::findOrFail( $address_id );
        $address['users'] = $address->user;
        return $address;
    }

    /*
     * Delete Address
     */
    public function delete( $address_id ) {

        $address = Order::findOrFail( $address_id );

        if ( $address ) {
            $address->deleted_at = Carbon::now();
            $address->save();
            return response()->json( ['message' => 'Address has been deleted', 'address' => $address], 200 );
        }
    }

}
