<?php

namespace App\Http\Controllers;
use Socialite;
use App\User;
use App\Mail\WelcomeEmail;
use App\Services\GHLService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class GoogleController extends Controller
{
    //
    public function redirectToGoogle()
    {
        // dd(env('GOOGLE_CLIENT_ID'));
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(), // ✅ Automatically verified
                'password' => bcrypt(str_random(16)), // placeholder password
            ]
        );

        // ✅ Send welcome email only the first time they register
        if ($user->wasRecentlyCreated) {
            session()->flash('fb_start_trial', true);
  
            try {
                $ghl = new GHLService();
                $ghl->startFreeTrial($user);
                $ghl->sendSignupWebhook($user, 'google');
            } catch (\Exception $e) {
                \Log::error('GHL Signup Webhook Error: ' . $e->getMessage());
            }

            Mail::to($user->email)->send(new WelcomeEmail($user));
           
        }

        Auth::login($user);

  
        return redirect('/')->with('success', 'Welcome, ' . $user->name . '!');
    }
}
