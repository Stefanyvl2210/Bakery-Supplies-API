<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserlogTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'userlog', function ( Blueprint $table ) {
            $table->id();
            $table->unsignedBigInteger( 'user_id' );
            $table->foreign( 'user_id' )
                ->references( 'id' )
                ->on( 'users' )
                ->onUpdate( 'cascade' )
                ->onDelete( 'cascade' );
            $table->string( 'action' );
            $table->timestamps();
        } );

        Schema::table( 'userlog', function ( Blueprint $table ) {
            $table->unsignedBigInteger( 'userlog_previous_id' )->nullable();
            $table->foreign( 'userlog_previous_id' )
                ->references( 'id' )
                ->on( 'userlog' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'userlog', function ( Blueprint $table ) {
            $table->dropForeign( ['userlog_previous_id'] );
            $table->dropColumn( 'userlog_previous_id' );
        } );
        Schema::dropIfExists( 'userlog' );
    }
}
