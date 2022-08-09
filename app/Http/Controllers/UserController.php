<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    /**
     * Show users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::query();
        $users = $query->get();
        $userId = $request->get('id') ?? null;

        if ($userId) {
            $users = User::find($userId);
            if(!$users)
                return response()->json(['Error' => "User with id ". $userId ." doesn't exist"], 404);

        } //else {
//            foreach ($users as $user) {
//                $orders = $user->orders;
//            }
      //  }

        return response()->json([
            'data' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'phone_number' => 'required|string',
            'image' => 'string',
        ]);

        try {
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

        } catch (\Throwable $e) {
            return response($e, 500);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'data' => $user,
            'token' => $token
        ];

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $user = User::find($id);
        if(!$user)
            return response()->json(['Error' => "User with id ". $id ." doesn't exist"], 404);

        try {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

        } catch (\Throwable $e) {
            return response()->json($e, 500);
        }

        $response = [
            'data' => $user,
        ];

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user)
            return response()->json(['Error' => "User with id ". $id ." doesn't exist"], 404);

        try {
            $delete = $user->delete();
        } catch (\Throwable $e) {
            return response()->json($e, 500);
        }

        return response()->json([
            'data' => [
                'message' => 'User with id '.$id.' has been deleted'
            ]
        ]);
    }

}
