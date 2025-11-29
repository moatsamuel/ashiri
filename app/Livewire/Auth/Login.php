<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Providers\SecurityServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class Login extends Component
{
    public $email;
    public $password;
    public $remember = false;

    protected $rules = [
        'email' => ['required', 'email'],
        'password' => ['required'],
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function login()
    {
        $this->validate();

        $ip = request()->ip();
        $userAgent = request()->userAgent();
        $security = app('security');

        // Check IP lockout
        if ($security->isIpLockedOut($ip)) {
            $remainingTime = $security->getRemainingLockoutTime($ip, 'ip');
            session()->flash('error', "Too many failed attempts from your IP. Please try again in {$remainingTime} minutes.");
            return;
        }

        // Check email lockout
        if ($security->isEmailLockedOut($this->email)) {
            $remainingTime = $security->getRemainingLockoutTime($this->email, 'email');
            session()->flash('error', "Too many failed attempts for this account. Please try again in {$remainingTime} minutes.");
            return;
        }

        // Check if user exists and is banned
        $user = User::where('email', $this->email)->first();
        
        if ($user && $user->isBanned()) {
            $security->recordLoginAttempt($this->email, $ip, false, $userAgent);
            session()->flash('error', 'Your account has been banned. Reason: ' . ($user->ban_reason ?? 'Policy violation'));
            return;
        }

        // Attempt login
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            // Update last login info
            $user = Auth::user();
            $user->update([
                'last_login_ip' => $ip,
                'last_login_at' => Carbon::now(),
            ]);

            $security->recordLoginAttempt($this->email, $ip, true, $userAgent);
            $security->clearLockout($ip, 'ip');
            $security->clearLockout($this->email, 'email');

            session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        // Record failed attempt
        $security->recordLoginAttempt($this->email, $ip, false, $userAgent);

        session()->flash('error', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}