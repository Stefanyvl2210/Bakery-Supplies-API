<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller {

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request                         $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store( Request $request ) {

        $data = $request->validate( [
            'name'               => 'required|string',
            'description'        => 'string',
            'price'              => 'required|numeric',
            'quantity_available' => 'numeric',
            'categories'         => 'string',
        ] );

        try {
            $product = Product::create( $data );

            /*
             * Assign categories
             */
            if ( $request->categories ) {
                $categories = json_decode( $data['categories'] );
                if ( count( $categories ) > 0 ) {
                    foreach ( $data['categories'] as $category ) {
                        array_push( $categories, $category );
                    }
                    $product->categories()->sync( $categories );
                }
            }

        } catch ( \Throwable $e ) {
            return response( $e, 500 );
        }

        return response()->json( ['message' => 'Product has been created', 'product' => $product], 200 );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request                         $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create_category( Request $request ) {

        $data = $request->validate( [
            'name' => 'required|string',
            'slug' => 'required|string',
        ] );

        try {
            $category = ProductCategory::create( $data );
        } catch ( \Throwable $e ) {
            return response( $e, 500 );
        }

        return response()->json( ['message' => 'Product Category has been created', 'category' => $category], 200 );
    }

    /*
     * Get all products
     */
    public function show_products( Request $request ) {
        return Product::orderBy( 'created_at', 'desc' )->where( 'deleted_at', null )->get();
    }

    /*
     * Get all categories
     */
    public function show_categories( Request $request ) {
        return ProductCategory::orderBy( 'created_at', 'desc' )->get();
    }

    /*
     * Get specific product
     */
    public function get_product( $product_id ) {
        $product               = Product::findOrFail( $product_id );
        $product['categories'] = $product->categories;
        return $product;
    }

    /*
     * Get specific category
     */
    public function get_category( $category_slug ) {
        $category = ProductCategory::where( 'slug', $category_slug )->first();
        if ( $category ) {
            $category['products'] = $category->products;
            return $category;
        }
        return response()->json( ['message' => 'Product Category not found', 'category' => $category], 400 );
    }

    /*
     * Update Product
     */
    public function update_product( Request $request, $product_id ) {

        $data = $request->validate( [
            'name'               => 'required|string',
            'description'        => 'string',
            'price'              => 'required|numeric',
            'quantity_available' => 'numeric',
            'categories'         => 'string',
        ] );

        $product = Product::findOrFail( $product_id );

        if ( $product ) {
            $product->update( $data );

            /*
             * Assign categories
             */
            if ( $request->categories ) {
                $categories = json_decode( $data['categories'] );
                if ( count( $categories ) > 0 ) {
                    foreach ( $data['categories'] as $category ) {
                        array_push( $categories, $category );
                    }
                    $product->categories()->sync( $categories );
                }
            }

            $product->save();
            return response()->json( ['message' => 'Product has been updated', 'product' => $product], 200 );
        }

    }

    /*
     * Update Product Category
     */
    public function update_category( Request $request, $category_id ) {

        $data = $request->validate( [
            'name' => 'required|string',
            'slug' => 'required|string',
        ] );

        $category = ProductCategory::findOrFail( $category_id );

        if ( $category ) {
            $category->update( $data );
            return response()->json( ['message' => 'Product Category has been updated', 'category' => $category], 200 );
        }

    }

    /*
     * Delete Product
     */
    public function delete_product( $product_id ) {

        $product = Product::findOrFail( $product_id );

        if ( $product ) {
            $product->deleted_at = Carbon::now();
            $product->save();
            return response()->json( ['message' => 'Product has been deleted', 'product' => $product], 200 );
        }
    }

    /*
     * Delete Product Category
     */
    public function delete_category( $category_id ) {

        $category = ProductCategory::findOrFail( $category_id );

        if ( $category ) {
            $category->delete();
            return response()->json( ['message' => 'Product Category has been deleted', 'category' => $category], 200 );
        }
    }

}
