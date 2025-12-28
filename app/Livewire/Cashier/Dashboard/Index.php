<?php

namespace App\Livewire\Cashier\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Cashier Dashboard')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.cashier.dashboard.index');
    }
}
