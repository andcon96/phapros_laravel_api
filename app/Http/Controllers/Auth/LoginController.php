<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User2;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    protected function authenticated(Request $request)
    {

        // Ambil Username Session
        $username = $request->input('username');
        $id = Auth::id();

        // Set Session Username & ID
        Session::put('userid', $id);
        $request->session()->put('username', $username);
    
        //new
        $users = User2::where('id', $id)->first();
        
        // set Session Flag buat Menu Access
        if ($users->can_access_web == 0) {
            Auth::logout();
            return redirect()->back()->with(['error' => 'User tidak memiliki akses']);
        } else {
            if ($users->is_active == 0) {
                Auth::logout();
                return redirect()->back()->with(['error' => 'User tidak aktif']);
            } else {
                Session::put('name', $users->name);
                Session::put('username', $users->username);
                Session::put('aksesweb', $users->can_access_web);

                if (session()->get('url.now') != null) {
                    // buat redirect ke prev url klo ada.
                    return redirect(session()->get('url.now'));
                }
            }
        }
    }


}
