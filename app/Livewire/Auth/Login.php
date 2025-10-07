<?php
namespace App\Livewire\Auth;

use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

#[Title('Login')]
class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $key = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', "Too many attempts. Please try again {$seconds} seconds.");
            return;
        }

        if (
            Auth::attempt([
                'email' => $this->email,
                'password' => $this->password,
            ], $this->remember)
        ) {
            RateLimiter::clear($key);
            session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard.index');
            } elseif ($user->role === 'user') {
                return redirect()->route('user.dashboard.index');
            } else {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Role not identified.',
                ]);
            }
        }

        RateLimiter::hit($key, 60);
        $this->addError('email', 'Email or password wrong.');
    }


    private function throttleKey()
    {
        return Str::lower($this->email) . '|' . request()->ip();
    }
    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.auth');
    }
}
