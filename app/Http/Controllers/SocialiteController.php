<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialiteController extends Controller
{
    //
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
        // dd($user);

        $existUser = User::where('email', $user->email)->first();
        $findUser = User::where('google_id', $user->id)->first();

        if ($findUser) {
            Auth::login($findUser);
            return redirect()->intended('dashboard');
        }

        if ($existUser != '' && $existUser->email === $user->email) {
            $existUser->google_id = $user->id;
            $existUser->save();
            Auth::login($existUser);
            return redirect()->intended('dashboard');
        } else {
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'password' => encrypt('fromsocialwebsite')
            ]);
            Auth::login($newUser);
            return redirect()->intended('dashboard');
        }
        // test
    }
}
