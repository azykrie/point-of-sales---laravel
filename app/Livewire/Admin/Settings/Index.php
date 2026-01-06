<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Settings')]
class Index extends Component
{
    use WithFileUploads;

    public $store_name = '';
    public $store_address = '';
    public $store_phone = '';
    public $store_email = '';
    public $receipt_footer = '';
    public $tax_enabled = false;
    public $tax_percentage = 11;
    public $tax_name = 'PPN';
    
    public $logo;
    public $logo_dark;
    
    public $current_logo = '';
    public $current_logo_dark = '';

    protected $rules = [
        'store_name' => 'required|string|max:255',
        'store_address' => 'required|string|max:500',
        'store_phone' => 'nullable|string|max:20',
        'store_email' => 'nullable|email|max:255',
        'receipt_footer' => 'nullable|string|max:255',
        'tax_enabled' => 'boolean',
        'tax_percentage' => 'required|numeric|min:0|max:100',
        'tax_name' => 'required|string|max:50',
        'logo' => 'nullable|image|max:2048',
        'logo_dark' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $defaults = Setting::getDefaults();
        
        $this->store_name = Setting::get(Setting::STORE_NAME, $defaults[Setting::STORE_NAME]);
        $this->store_address = Setting::get(Setting::STORE_ADDRESS, $defaults[Setting::STORE_ADDRESS]);
        $this->store_phone = Setting::get(Setting::STORE_PHONE, $defaults[Setting::STORE_PHONE]);
        $this->store_email = Setting::get(Setting::STORE_EMAIL, $defaults[Setting::STORE_EMAIL]);
        $this->receipt_footer = Setting::get(Setting::RECEIPT_FOOTER, $defaults[Setting::RECEIPT_FOOTER]);
        $this->tax_enabled = (bool) Setting::get(Setting::TAX_ENABLED, $defaults[Setting::TAX_ENABLED]);
        $this->tax_percentage = (float) Setting::get(Setting::TAX_PERCENTAGE, $defaults[Setting::TAX_PERCENTAGE]);
        $this->tax_name = Setting::get(Setting::TAX_NAME, $defaults[Setting::TAX_NAME]);
        $this->current_logo = Setting::get(Setting::STORE_LOGO);
        $this->current_logo_dark = Setting::get(Setting::STORE_LOGO_DARK);
    }

    public function save()
    {
        $this->validate();

        // Save text settings
        Setting::set(Setting::STORE_NAME, $this->store_name);
        Setting::set(Setting::STORE_ADDRESS, $this->store_address);
        Setting::set(Setting::STORE_PHONE, $this->store_phone);
        Setting::set(Setting::STORE_EMAIL, $this->store_email);
        Setting::set(Setting::RECEIPT_FOOTER, $this->receipt_footer);
        Setting::set(Setting::TAX_ENABLED, $this->tax_enabled ? '1' : '0');
        Setting::set(Setting::TAX_PERCENTAGE, $this->tax_percentage);
        Setting::set(Setting::TAX_NAME, $this->tax_name);

        // Handle logo upload
        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            Setting::set(Setting::STORE_LOGO, 'storage/' . $logoPath);
            $this->current_logo = 'storage/' . $logoPath;
            $this->logo = null;
        }

        // Handle dark logo upload
        if ($this->logo_dark) {
            $logoDarkPath = $this->logo_dark->store('logos', 'public');
            Setting::set(Setting::STORE_LOGO_DARK, 'storage/' . $logoDarkPath);
            $this->current_logo_dark = 'storage/' . $logoDarkPath;
            $this->logo_dark = null;
        }

        Flux::toast('Settings saved successfully!', variant: 'success');
    }

    public function removeLogo()
    {
        Setting::set(Setting::STORE_LOGO, null);
        $this->current_logo = '';
        Flux::toast('Logo removed!', variant: 'success');
    }

    public function removeLogoDark()
    {
        Setting::set(Setting::STORE_LOGO_DARK, null);
        $this->current_logo_dark = '';
        Flux::toast('Dark logo removed!', variant: 'success');
    }

    public function render()
    {
        return view('livewire.admin.settings.index');
    }
}
