<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User2;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        // dd($req->all());
        $userweblist = User2::query();
        if($req->s_username){
            $userweblist = $userweblist->where('username',$req->s_username);
        }
        
        if($req->s_name){
            $userweblist = $userweblist->where('name',$req->s_name);
        }
        $userweblist = $userweblist->paginate(10);


        $userkaryawanlist = User::get();
        

        return view('setting.users.index', compact('userweblist','userkaryawanlist'));
        
        

        

        
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $username = $request->username;
        $password = null;
        $canaccessweb = '0';
        
        if($request->checkaccessweb){
            $this->validate($request, [
                'username' => 'required|unique:users',
                'password' => 'required|max:20',
                'password_confirmation' => 'required|max:20|same:password',
            ], [
                'unique' => 'Username Must Be Unique',
            ]);
            $password = $request->password;
            $canaccessweb = '1';         
        }
        DB::beginTransaction();
        
        try{
            $user = User2::where('username',$username)->first();
            $user = new User2();
            $user->username = $request->username;
            $user->name = $request->name;
            $user->can_access_web = $canaccessweb;
            if($password != null){
                $user->password = Hash::make($password);
            }
            $user->save();
            DB::commit();
            alert()->success('Success', 'User successfully created!');
            return redirect()->route('usermaint.index');
        }
        catch(Exception $err){
            DB::rollBack();
            alert()->error('Error', 'User failed to created!');
            return redirect()->route('usermaint.index');
        }
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User2  $user2
     * @return \Illuminate\Http\Response
     */
    public function show(User2 $user2)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User2  $user2
     * @return \Illuminate\Http\Response
     */
    public function edit(User2 $user2)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User2  $user2
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        $username = $request->username;
        $password = null;
        $canaccessweb = '0';
        
        if($request->checkaccessweb){
            
                $this->validate($request, [
                    
                    'password' => 'required|max:20',
                    'password_confirmation' => 'required|max:20|same:password',
                ], [
                    
                ]);
                $password = $request->password;
                $canaccessweb = '1';
            
        }
        DB::beginTransaction();
        try{
            $user = User2::where('username',$username)->first();
            $user->can_access_web = $canaccessweb;
            if($password != null){
                $user->password = Hash::make($password);
            }
            $user->save();
            DB::commit();
            alert()->success('Success', 'User successfully updated!');
            return redirect()->route('usermaint.index');
        }
        catch(Exception $err){
            DB::rollBack();
            alert()->error('Error', 'User failed to update!');
            return redirect()->route('usermaint.index');
        }
        

        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User2  $user2
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        
        DB::beginTransaction();
        try{
            $user = User2::where('id',$request->temp_id)->delete();
            DB::commit();
            alert()->error('Success', 'User deleted!');
            return redirect()->route('usermaint.index');
        }
        catch(Exception $err){
            DB::rollback();
            alert()->error('Error', 'User failed to delete!');
            return redirect()->route('usermaint.index');

        }   
        

        //
    }
}