<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Edit User')]
class Edit extends Component
{
    public $name, $email, $role, $password, $password_confirmation, $userId;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->userId,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required',
        ];
    }

    public function mount($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
    }

    public function update()
    {
        $this->validate();

        $user = User::findOrFail($this->userId);
        $user->name = $this->name;
        $user->email = $this->email;
        $user->role = $this->role;

        if ($this->password) {
            $user->password = bcrypt($this->password);
        }

        $user->save();

        session()->flash('success', 'User updated successfully!');
        return $this->redirect(route('admin.users.index'), navigate: true);
    }
    public function render()
    {
        return view('livewire.admin.users.edit');
    }
}
