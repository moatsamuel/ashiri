<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Livewire\Component;
use Illuminate\Support\Str;
// use Livewire\Attributes\Layout;

//  #[Layout('components.layouts.app')]
class Register extends Component
{
    public $name;
    public $username;
    public $email;
    public $password;
    public $password_confirmation;
    public $terms = false;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users,username',
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ];
    }

    protected $messages = [
        'username.regex' => 'Username can only contain letters, numbers, and underscores.',
        'username.unique' => 'This username is already taken. Please choose another one.',
        'terms.accepted' => 'You must accept the terms and conditions.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'username' => $this->username,
            'profile_slug' => Str::slug($this->username),
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        session()->flash('success', 'Registration successful! Please verify your email address.');

        return redirect()->route('verification.notice');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}