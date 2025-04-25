<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
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

    /**
     * Add additional customizaion process after user is authenticated
     *
     * @param $request <Request>
     * @param $user <User> - the authentication user instance
     *
     * @return <route>
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect(RouteServiceProvider::HOME);
    }
   
   
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [
            $this->username() => trans('auth.failed'),
        ];
    
        // Check if the provided password is incorrect
        if ($this->guard()->getProvider()->retrieveByCredentials($this->credentials($request))) {
            $errors = [
                'password' => trans('auth.these_credentials_do_not_match'),
            ];
        }
    
        throw ValidationException::withMessages($errors);
    }
    

    
}
