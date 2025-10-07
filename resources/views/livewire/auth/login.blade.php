<div>
    <div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-zinc-900 p-4">
        <div
            class="w-full sm:max-w-sm md:max-w-md lg:max-w-lg xl:max-w-xl 
                p-6 sm:p-8 md:p-10 lg:p-14 
                bg-white rounded-lg shadow-lg dark:bg-zinc-800">
            <form wire:submit.prevent="login" class="space-y-4">
                <div class="text-2xl font-semibold text-center">Welcome</div>

                <div>
                    <flux:input wire:model="email" label="Email" type="email" placeholder="Enter Your email" />
                </div>

                <div>
                    <flux:input wire:model="password" type="password" label="Password" placeholder="Enter Your password"
                        viewable />
                </div>

                <div>
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="remember" />
                        <flux:label>Remember me</flux:label>
                    </flux:field>
                </div>

                <div>
                    <flux:button class="w-full" variant="primary" type="submit">Login</flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
