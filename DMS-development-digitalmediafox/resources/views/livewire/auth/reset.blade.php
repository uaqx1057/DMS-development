<x-ui.row class="justify-content-center">
    <x-ui.col class="col-md-8 col-lg-6 col-xl-5">
        <x-ui.card class="mt-4 card-bg-fill">
            <x-ui.card-body class="p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">@translate('Reset Password')</h5>
                    <p class="text-muted">@translate('Set a new password for your account.')</p>
                </div>

               @if (session('status'))
                <div class="alert alert-success mb-3">
                {{ session('status') }}
                </div>
                @endif
                
                @if (session('error'))
                <div class="alert alert-danger mb-3">
                {{ session('error') }}
                </div>
                @endif

      
            @if(!$invalidToken)
            <form wire:submit.prevent="resetPass">
            <input type="hidden" wire:model="token" />
            <input type="hidden" wire:model="email" />
            
            <!-- New Password -->
            <div class="mb-3">
            <x-form.label for="password" :name="@translate('New Password')" />
            <div class="input-group">
            <input type="password" wire:model="password" id="password" class="form-control" placeholder="Enter new password">
            <button type="button" class="btn btn-outline-secondary" id="password-addon">
            <i class="ri-eye-off-fill"></i>
            </button>
            </div>
            <x-ui.alert error="password" />
            </div>
            
            <!-- Confirm Password -->
            <div class="mb-3">
            <x-form.label for="password_confirmation" :name="@translate('Confirm Password')" />
            <div class="input-group">
            <input type="password" wire:model="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm password">
            <button type="button" class="btn btn-outline-secondary" id="password-confirmation-addon">
            <i class="ri-eye-off-fill"></i>
            </button>
            </div>
            <x-ui.alert error="password_confirmation" />
            </div>

            
            <x-ui.button class="btn-primary w-100 mt-3">@translate('Reset Password')</x-ui.button>
            </form>
            @endif
             <div class="float-end">
                        <a href="{{ route('login') }}" class="text-muted">
                        @translate('Login')
                        </a>
                        
                        </div>

            </x-ui.card-body>
        </x-ui.card>
    </x-ui.col>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggles = [
            { inputId: 'password', buttonId: 'password-addon' },
            { inputId: 'password_confirmation', buttonId: 'password-confirmation-addon' }
        ];

        toggles.forEach(({ inputId, buttonId }) => {
            const toggleBtn = document.getElementById(buttonId);
            const passwordInput = document.getElementById(inputId);
            const icon = toggleBtn.querySelector('i');

            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                icon.classList.toggle('ri-eye-fill', !isPassword);
                icon.classList.toggle('ri-eye-off-fill', isPassword);
            });
        });
    });
</script>

</x-ui.row>

