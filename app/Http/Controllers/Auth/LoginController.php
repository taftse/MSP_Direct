<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/home';

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
     * Redirect the user to the Facebook authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        try{
            $user = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            dd($e);
            //return Redirect::to('login');
        }
        
    }

    $authUser = $this->findOrCreateUser($user);
    Auth::login($authUser, false);
     
      return Redirect::route('dashboard');
    }
    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $githubUser
     * @return User
     */
    private function findOrCreateUser($fbUser)
    {
        if ($authUser = User::find($fbUser->id)->first()) {
            $authUser->access_token = $fbUser->token;
            $authUser->refresh_token = $fbUser->refreshToken;
            $authUser->save();
            return $authUser;
        }
        return User::create([
            'name' => $fbUser->name,
            'email' =>$fbUser->email,
            'id' => $fbUser->id,
            'access_token'  => $fbUser->token,
            'refresh_token' => $fbUser->refreshToken
        ]);
    }
}
