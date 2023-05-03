<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class APIController extends Controller
{
    public $successStatus = 200;

    public function login()
    {
        // if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            // $user = Auth::user();
        //     $nUser = User::find($user->id)->first();
        //     // dd($user);
        //     $success['token'] =  $user->createToken('nApp')->accessToken;
        //     return response()->json(
        //         ['message' => 'Sukses',
        //          'user' => $nUser,
        //          'username' => $user->id, 
        //          'success' => $success], $this->successStatus);
        // }
        // else{
        //     return response()->json(['message' => 'Error', 'error'=>'Unauthorised'], 401);
        // }
        $usercheck = User2::where('username',request('nik'))->first();
        
        if($usercheck){
            $user = User::where('nik', request('nik'))->first();

            if ($user) {
                if (md5(request('password')) == $user->password) {

                    $success['token'] =  $user->createToken('nApp')->accessToken;
                    return response()->json(
                        [
                            'message' => 'Sukses',
                            'user' => $user,
                            'username' => $user->id,
                            'success' => $success,
                            'user_approver' => $usercheck->user_approver
                        ],
                        $this->successStatus
                    );
                } else {
                    $response = ["message" => "Error"];
                    return response($response, 422);
                }
            } else {
                return response()->json(['message' => 'Error', 'error' => 'Unauthorised'], 401);
            }
        }
        else {
            return response()->json(['message' => 'Error', 'error' => 'Unauthorised'], 401);
        }
        
    }

    public function resetPass(Request $request)
    {
        $id = $request->input('id');
        $password = $request->input('password');
        $confpass = $request->input('confpass');
        $oldpass = $request->input('oldpass');

        $hasher = app('hash');

        $users = DB::table("users")
            ->select('id', 'password')
            ->where("users.id", $id)
            ->first();

        if ($hasher->check($oldpass, $users->password)) {
            if ($password != $confpass) {
                return response()->json(['message' => 'Error', 'error' => 'Confirm & New Doesnt Match'], 401);
            } else {
                DB::table('users')
                    ->where('id', $id)
                    ->update(['password' => Hash::make($password)]);

                return response()->json([
                    'message' => 'Success',
                ], 200);
            }
        } else {
            return response()->json(['message' => 'Error', 'error' => 'Old Pass is wrong'], 401);
        }
    }
}
