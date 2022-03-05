<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"User"},
     *     description="Get users",
     *     @OA\Response(response="default", description="Get users")
     * )
     */
    public function getUsers(Request $request){

        $users = User::query();

        if ($request->has('first_name')){
            $users->where('first_name','LIKE', "%".$request->first_name."%");
        }
        if ($request->has('last_name')){
            $users->where('last_name','LIKE', "%".$request->last_name."%");
        }
        if ($request->has('email')){
            $users->where('email',$request->email);
        }
        if ($request->has('phone_number')){
            $users->where('phone_number','LIKE', "%".$request->phone_number."%");
        }

        return $users->get();
    }

    /**
     * @OA\Put(
     *     path="/api/users",
     *     tags={"User"},
     *     description="Update users",
     *     @OA\Response(response="default", description="Update users")
     * )
     */
    public function updateUser(Request $request,$id){

        $user = User::find($id);

        if (!$user){
            return ["message"=>"User not found"];
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|digits:10',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone_number = $request->phone_number;
        $user->save();

        return response()->json([
            'message' => 'User updated',
            'user' => $user,
        ]);
    }

    /**
     * @OA\Delete (
     *     path="/users",
     *     tags={"User"},
     *     description="Delete user",
     *     @OA\Response(response="default", description="Delete user")
     * )
     */
    public function deleteUser($id){

        $user = User::find($id);

        if (!$user){
            return ["message"=>"User not found"];
        }
        $user->delete();

        return ["message"=>"User deleted"];
    }
}
