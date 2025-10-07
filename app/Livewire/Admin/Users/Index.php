<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Users Table')]
class Index extends Component
{
    use WithPagination;
    public $search = '';
    public $deleteUserId;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($userId)
    {
        $this->deleteUserId = $userId;
    }

    public function delete()
    {
        User::destroy($this->deleteUserId);
        Flux::toast('User deleted successfully!', variant:'success');
        Flux::modals()->close();
    }
    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('role', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(5);

        return view('livewire.admin.users.index', compact('users'));
    }
}
