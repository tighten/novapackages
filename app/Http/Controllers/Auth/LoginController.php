<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\RedirectResponse;
use App\Events\NewUserSignedUp;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller implements HasMiddleware
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
    protected $redirectTo = '/';

    public static function middleware(): array
    {
        return [
            new Middleware('guest', except: ['logout']),
        ];
    }

    public function redirectToProvider()
    {
        if (app()->environment('local')) {
            Auth::login(User::findOrFail(1));

            return redirect()->intended('/');
        }

        session()->flash('url.intended', url()->previous());

        return Socialite::driver('github')->redirect();
    }

    public function handleProviderCallback(): RedirectResponse
    {
        $user = $this->createOrUpdateUser(Socialite::driver('github')->user());

        if ($user->wasRecentlyCreated) {
            event(new NewUserSignedUp($user));
        }

        Auth::login($user, false);

        return redirect()->intended();
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
            'github_user_id' => $socialiteUser->getId(),
        ];
    }
}
