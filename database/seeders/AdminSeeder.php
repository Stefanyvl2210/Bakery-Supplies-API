<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $data = [
            'first_name'   => 'System',
            'last_name'    => 'Admin',
            'email'        => 'supply@getnada.com',
            'password'     => Hash::make( '12345' ),
            'phone_number' => '000000',
        ];
        $user                    = User::create( $data );
        $user->email_verified_at = Carbon::now();
        $user->createToken( 'authToken' )->plainTextToken;
        $user->assignRole( 'admin' );
        $user->assignRole( 'user' );
        $user->save();

    }
}
