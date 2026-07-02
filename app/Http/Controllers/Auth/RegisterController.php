<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\TeamInvitation;
use App\Http\Controllers\TeamGroupController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Services\GHLService;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'invitation_token' => ['nullable', 'string'],
        ]);
    }

    public function showRegistrationForm(Request $request)
    {
        $invitation = null;

        if ($request->filled('invite')) {
            $invitation = TeamInvitation::with('group')
                ->where('token', $request->invite)
                ->first();
        }

        return view('auth.register', compact('invitation'));
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['invitation_token'])) {
            $invitation = TeamInvitation::where('token', $data['invitation_token'])->first();

            if ($invitation && $invitation->isPending() && strtolower($invitation->email) === strtolower($user->email)) {
                TeamGroupController::acceptInvitation($invitation, $user->id);
            }
        }

        try {
            // dd('renz');
            $ghl = new GHLService();
            $ghl->startFreeTrial($user);
            $ghl->createContact([
                'firstName' => $user->name,
                'email' => $user->email,
                'tags' => ['Saltiii Registration'],
            ]);
            $ghl->sendSignupWebhook($user, 'email');
        } catch (\Exception $e) {
            \Log::error('GHL API Error: ' . $e->getMessage());
        }
        return $user;
    }

    protected function registered($request, $user)
    {
        session()->flash('fb_start_trial', true);
    }
}
