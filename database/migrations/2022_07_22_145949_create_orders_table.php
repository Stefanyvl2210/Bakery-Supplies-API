<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'orders', function ( Blueprint $table ) {
            $table->id();
            $table->binary( 'is_guest' );
            $table->string( 'guest_data' )->nullable();
            $table->string( 'guest_address' )->nullable();
            $table->string( 'status' );
            $table->string( 'delivery_type' );
            $table->float( 'taxes' )->default( 0 );
            $table->float( 'total' );
            $table->timestamp( 'estimate_delivery' )->nullable();
            $table->timestamp( 'delivery_time' )->nullable();
            $table->timestamps();
            $table->softDeletes();
        } );
        Schema::table( 'orders', function ( Blueprint $table ) {
            $table->unsignedBigInteger( 'user_id' )->nullable();
            $table->foreign( 'user_id' )
                ->references( 'id' )
                ->on( 'users' )
                ->onUpdate( 'cascade' )
                ->onDelete( 'cascade' );
            $table->unsignedBigInteger( 'payment_id' )->nullable();
            $table->foreign( 'payment_id' )
                ->references( 'id' )
                ->on( 'payments' )
                ->onUpdate( 'cascade' )
                ->onDelete( 'cascade' );
            $table->unsignedBigInteger( 'address_id' )->nullable();
            $table->foreign( 'address_id' )
                ->references( 'id' )
                ->on( 'addresses' )
                ->onUpdate( 'cascade' )
                ->onDelete( 'cascade' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'orders', function ( Blueprint $table ) {
            $table->dropForeign( ['user_id'] );
            $table->dropColumn( 'user_id' );
            $table->dropForeign( ['payment_id'] );
            $table->dropColumn( 'payment_id' );
            $table->dropForeign( ['address_id'] );
            $table->dropColumn( 'address_id' );
        } );
        Schema::dropIfExists( 'orders' );
    }
}
