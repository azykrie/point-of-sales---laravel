<div>
    <div class="flex items-center justify-between mb-6">
        <div class="text-2xl font-semibold">Settings</div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Store Information -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.building-storefront class="w-5 h-5 text-blue-500" />
                Store Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input wire:model="store_name" label="Store Name" placeholder="Enter store name" />

                <flux:input wire:model="store_phone" label="Phone Number" placeholder="08123456789" />

                <flux:input wire:model="store_email" type="email" label="Email" placeholder="store@example.com" />

                <div></div>

                <div class="md:col-span-2">
                    <flux:textarea wire:model="store_address" label="Store Address"
                        placeholder="Enter full store address" rows="2"
                        description="This will appear on receipts/invoices" />
                </div>
            </div>
        </div>

        <!-- Logo Settings -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.photo class="w-5 h-5 text-purple-500" />
                Logo Settings
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Light Logo -->
                <div>
                    <label class="block text-sm font-medium mb-2">Logo (Light Mode)</label>
                    <div class="border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4">
                        @if ($current_logo)
                            <div class="flex items-center gap-4 mb-3">
                                <img src="{{ asset($current_logo) }}" alt="Current Logo"
                                    class="h-12 object-contain bg-white p-2 rounded" />
                                <flux:button size="sm" variant="danger" wire:click="removeLogo" icon="trash">
                                    Remove
                                </flux:button>
                            </div>
                        @endif

                        <input type="file" wire:model="logo" accept="image/*"
                            class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />

                        @if ($logo)
                            <div class="mt-3">
                                <p class="text-xs text-zinc-500 mb-1">Preview:</p>
                                <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview"
                                    class="h-12 object-contain bg-white p-2 rounded" />
                            </div>
                        @endif

                        <p class="text-xs text-zinc-500 mt-2">Recommended: PNG with transparent background, max 2MB</p>
                    </div>
                    @error('logo')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Dark Logo -->
                <div>
                    <label class="block text-sm font-medium mb-2">Logo (Dark Mode)</label>
                    <div class="border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4">
                        @if ($current_logo_dark)
                            <div class="flex items-center gap-4 mb-3">
                                <img src="{{ asset($current_logo_dark) }}" alt="Current Dark Logo"
                                    class="h-12 object-contain bg-zinc-800 p-2 rounded" />
                                <flux:button size="sm" variant="danger" wire:click="removeLogoDark" icon="trash">
                                    Remove
                                </flux:button>
                            </div>
                        @endif

                        <input type="file" wire:model="logo_dark" accept="image/*"
                            class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" />

                        @if ($logo_dark)
                            <div class="mt-3">
                                <p class="text-xs text-zinc-500 mb-1">Preview:</p>
                                <img src="{{ $logo_dark->temporaryUrl() }}" alt="Dark Logo Preview"
                                    class="h-12 object-contain bg-zinc-800 p-2 rounded" />
                            </div>
                        @endif

                        <p class="text-xs text-zinc-500 mt-2">Optional: Use a lighter logo for dark mode</p>
                    </div>
                    @error('logo_dark')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Tax/PPN Settings -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.calculator class="w-5 h-5 text-orange-500" />
                Tax Settings (PPN)
            </h3>

            <div class="space-y-4">
                <flux:switch wire:model.live="tax_enabled" label="Enable Tax"
                    description="When enabled, tax will be calculated on all transactions" />

                @if ($tax_enabled)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <flux:input wire:model="tax_name" label="Tax Name" placeholder="PPN"
                            description="Name displayed on receipts (e.g., PPN, VAT, GST)" />

                        <flux:input wire:model="tax_percentage" type="number" step="0.1" min="0"
                            max="100" label="Tax Percentage (%)" placeholder="11"
                            description="Tax rate to apply (e.g., 11 for 11%)" />
                    </div>
                @endif
            </div>
        </div>

        <!-- Receipt Settings -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.receipt-percent class="w-5 h-5 text-green-500" />
                Receipt Settings
            </h3>

            <flux:input wire:model="receipt_footer" label="Receipt Footer Message"
                placeholder="Thank you for your purchase!"
                description="This message will appear at the bottom of receipts" />
        </div>

        <!-- Preview -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.eye class="w-5 h-5 text-amber-500" />
                Receipt Preview
            </h3>

            <div class="max-w-xs mx-auto bg-white border border-zinc-200 rounded-lg p-4 font-mono text-sm">
                <div class="text-center mb-3">
                    @if ($current_logo)
                        <img src="{{ asset($current_logo) }}" alt="Logo" class="h-10 mx-auto mb-2" />
                    @endif
                    <div class="font-bold">{{ $store_name ?: 'Store Name' }}</div>
                    <div class="text-xs text-zinc-500">{{ $store_address ?: 'Store Address' }}</div>
                    @if ($store_phone)
                        <div class="text-xs text-zinc-500">Telp: {{ $store_phone }}</div>
                    @endif
                </div>

                <div class="border-t border-dashed border-zinc-300 my-2"></div>

                <div class="text-xs space-y-1">
                    <div class="flex justify-between">
                        <span>Invoice:</span>
                        <span>INV202512300001</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Date:</span>
                        <span>{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="border-t border-dashed border-zinc-300 my-2"></div>

                <div class="space-y-1">
                    <div>
                        <div>Sample Product</div>
                        <div class="flex justify-between text-xs">
                            <span>2 x 10.000</span>
                            <span>20.000</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-dashed border-zinc-300 my-2"></div>

                <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                        <span>Subtotal</span>
                        <span>Rp 20.000</span>
                    </div>
                    @if ($tax_enabled)
                        <div class="flex justify-between text-xs">
                            <span>{{ $tax_name ?: 'PPN' }} ({{ $tax_percentage }}%)</span>
                            <span>Rp {{ number_format(20000 * ($tax_percentage / 100), 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold">
                        <span>TOTAL</span>
                        <span>Rp
                            {{ $tax_enabled ? number_format(20000 * (1 + $tax_percentage / 100), 0, ',', '.') : '20.000' }}</span>
                    </div>
                </div>

                <div class="border-t border-dashed border-zinc-300 my-2"></div>

                <div class="text-center text-xs text-zinc-500">
                    {{ $receipt_footer ?: 'Thank you!' }}
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <flux:button type="submit" variant="primary" icon="check">
                Save Settings
            </flux:button>
        </div>
    </form>
</div>
