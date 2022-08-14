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
            'address_id'        => 'required|numeric',
            'estimate_delivery' => 'required',
            'taxes'             => 'required|numeric',
            'total'             => 'required|numeric',
            'delivery_type'     => 'required|string',
            'is_guest'          => 'required|boolean',
            'status'            => 'required',
        ] );

        try {
            if ( empty( $data['user_id'] ) ) {
                $data['user_id'] = auth()->user()->id;
            }

            $address = Order::create( $data );
        } catch ( \Throwable $e ) {
            return response( $e, 500 );
        }

        return response()->json( ['message' => 'Order has been generated', 'address' => $address], 200 );
    }

    /**
     *
     * @param  Request                         $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( Request $request, $order_id ) {

        $data = $request->validate( [
            'delivery_time'     => 'required|string',
            'status'            => 'required',
            'estimate_delivery' => 'nullable',
        ] );

        $order = Order::findOrFail( $order_id );

        if ( $order ) {
            $order->update( $data );
        }

        return response()->json( ['message' => 'Order has been updated', 'order' => $order], 200 );
    }

    /*
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_all() {
        return Order::all();
    }

    /*
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_all_by_user() {
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
