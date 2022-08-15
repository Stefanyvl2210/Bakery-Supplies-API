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
            'products'          => 'string|required',
        ] );

        try {
            if ( empty( $data['user_id'] ) ) {
                $data['user_id'] = auth()->user()->id;
            }

            $order = Order::create( $data );

            /*
             * Assign products
             */
            if ( $request->products ) {
                $products = json_decode( $data['products'] );
                if ( count( $products ) > 0 ) {
                    foreach ( $data['products'] as $product ) {
                        array_push( $products, $product );
                    }
                    $product->categories()->sync( $products );
                }
            }

        } catch ( \Throwable $e ) {
            return response( $e, 500 );
        }

        return response()->json( ['message' => 'Order has been generated', 'order' => $order], 200 );
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
    public function get_all_by_user( $user_id ) {
        if ( is_null( $user_id ) ) {
            $user_id = auth()->user()->id;
        }

        return Order::orderBy( 'created_at', 'desc' )->where( 'user_id', $user_id )->where( 'deleted_at', null )->get();
    }

    /*
     *
     * @param  Request                         $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get( $order_id ) {
        $order          = Order::findOrFail( $order_id );
        $order['users'] = $order->user;
        return $order;
    }

    /*
     * Delete Address
     */
    public function delete( $order_id ) {

        $order = Order::findOrFail( $order_id );

        if ( $order ) {
            $order->deleted_at = Carbon::now();
            $order->save();
            return response()->json( ['message' => 'Order has been deleted', 'order' => $order], 200 );
        }
    }

}
