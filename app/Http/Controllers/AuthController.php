<?php

namespace App\Http\Controllers;

use Socialite;
use Auth;
use App\User;
use Exception;

class AuthController extends Controller
{

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->scopes([])->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('github')->user();
            $authUser = $this->findOrCreateUser($user);

            Auth::login($authUser, true);
        } catch (Exception $e) {
            // just catch it...
        }

        return redirect('/');
    }

    /**
     * Log the current user out.
     *
     * @return Response
     */
    public function signOut()
    {
        Auth::logout();

        return redirect('/');
    }

    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $githubUser
     * @return User
     */
    private function findOrCreateUser($user)
    {
        if ($authUser = User::where('github_id', $user->id)->first()) {
            return $authUser;
        }

        return User::create([
            'name' => $user->name ? $user->name : $user->nickname,
            'github_username' => $user->nickname,
            'github_id' => $user->id,
            'github_token' => $user->token,
            'github_avatar' => $user->avatar
        ]);
    }

}
