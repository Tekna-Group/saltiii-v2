<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
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
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
    /**
 * The user has been verified.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return mixed
 */
public function verify(Request $request)
{
    $userID = $request->route('id');
    $user = \App\User::findOrFail($userID);

    if (! hash_equals((string) $userID, (string) $request->user()->getKey())) {
        throw new AuthorizationException;
    }

    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
        Mail::to($user->email)->send(new WelcomeEmail($user));
    }

    return redirect('/login')->with('success', 'Your account has been verified! Welcome email sent.');
}
protected function verified(Request $request)
{
    $user = \App\User::findOrFail($request->route('id'));

    // Send welcome email
    Mail::to($user->email)->send(new WelcomeEmail($user));

    return redirect('/login')->with('success', 'Your account has been verified! Welcome email sent.');
}

}
