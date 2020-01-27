<?php

namespace App\Http\Controllers\Auth;

use App\Events\NewUserSignedUp;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
    protected $redirectTo = '/app/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleProviderCallback()
    {
        $user = $this->createOrUpdateUser(Socialite::driver('github')->user());

        if ($user->wasRecentlyCreated) {
            event(new NewUserSignedUp($user));
        }

        Auth::login($user, false);

        return redirect()->route('home');
    }

    private function createOrUpdateUser($socialiteUser)
    {
        if (is_null($user = User::forSocialiteUser($socialiteUser))) {
            return User::create($this->socialiteUserAttributes($socialiteUser));
        }

        $user->update($this->socialiteUserAttributes($socialiteUser));

        return $user;
    }

    private function socialiteUserAttributes($socialiteUser)
    {
        return [
            'name' => $socialiteUser->getName() ?: $socialiteUser->getNickname(),
            'email' => $socialiteUser->getEmail(),
            'avatar' => $socialiteUser->getAvatar(),
            'github_username' => $socialiteUser->getNickname(),
        ];
    }
}
