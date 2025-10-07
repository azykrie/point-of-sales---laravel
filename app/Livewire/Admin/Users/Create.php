<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create User')]
class Create extends Component
{
    public $name, $email, $password, $password_confirmation, $role;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required',
        ];
    }

    public function save()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role' => $this->role,
        ]);

        session()->flash('success', 'User created successfully!');
        return $this->redirect(route('admin.users.index'), navigate: true);

    }
    public function render()
    {
        return view('livewire.admin.users.create');
    }
}
