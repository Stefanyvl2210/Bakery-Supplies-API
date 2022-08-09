<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $role = Role::create( ['name' => 'admin'] );

        $data = [
            'first_name'   => 'System',
            'last_name'    => 'Admin',
            'email'        => 'supply@getnada.com',
            'password'     => Hash::make( '12345' ),
            'phone_number' => '000000',
        ];
        $user = User::create( $data );
        $user->email_verified_at = Carbon::now();
        $user->createToken( 'authToken' )->plainTextToken;
        $user->assignRole( 'admin' );
        $user->save();
        
    }
}
