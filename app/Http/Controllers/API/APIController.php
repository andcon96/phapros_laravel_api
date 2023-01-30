<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class APIController extends Controller
{
    public $successStatus = 200;
    
    public function login()
    {
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $nUser = DB::table('users')
                    ->leftJoin('mcustomers','users.customer_id','mcustomers.id')
                    ->where('users.id','=',$user->id)
                    ->first();;
            // dd($user);
            $success['token'] =  $user->createToken('nApp')->accessToken;
            return response()->json(
                ['message' => 'Sukses',
                 'custid' => $user->customer_id,
                 'user' => $nUser,
                 'username' => $user->id, 
                 'success' => $success], $this->successStatus);
        }
        else{
            return response()->json(['message' => 'Error', 'error'=>'Unauthorised'], 401);
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
                    ->select('id','password')
                    ->where("users.id",$id)
                    ->first();

        if($hasher->check($oldpass,$users->password))
        {
            if($password != $confpass)
            {
                return response()->json(['message' => 'Error', 'error'=>'Confirm & New Doesnt Match'], 401);
            }else{
                DB::table('users')
                ->where('id', $id)
                ->update(['password' => Hash::make($password)]);

                return response()->json([
                    'message' => 'Success',
                ],200);
            }
        }else{
                return response()->json(['message' => 'Error', 'error'=>'Old Pass is wrong'], 401);
        }         
    }
}
