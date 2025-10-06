<x-ui.row class="justify-content-center">
    <x-ui.col class="col-md-8 col-lg-6 col-xl-5">
        <x-ui.card class="mt-4 card-bg-fill">
          
            <x-ui.card-body class="p-4">
                
                
                <div class="text-center text-white-50">
                            <div>
                                <a href="login" class="d-inline-block">
                                   <img src="{{ asset('public/logo.png') }}" alt="{{ $title ?? env('APP_NAME') }}" width="200px">
        
                                </a>
                            </div>
                        </div>
                
                
                <!--<div class="text-center mt-2">-->
                <!--    <h5 class="text-primary">@translate('Welcome Back !')</h5>-->
                <!--    <p class="text-muted">@translate('Sign in to continue to iLab OMS.')</p>-->
                <!--</div>-->

                <div class="p-2 mt-4">
                    <form method="POST" action="{{ route('custom.login') }}" autocomplete="on">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <x-form.label for="email" :name="@translate('Email')" :required=true />
                            <x-form.input-email
                                id="email"
                                name="email"
                                placeholder="Enter email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                            />
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password with Eye Icon --}}
                        <div class="mb-3">
                            
                            <div class="float-end">
                            <a href="{{ route('admin.password.request') }}" class="text-muted">
                            @translate('Forgot password?')
                            </a>
                            </div>
                            <x-form.label for="password" :name="@translate('Password')" :required=true />
                            <div class="input-group">
                                <x-form.input-password
                                    id="password"
                                    name="password"
                                    placeholder="Enter password"
                                    autocomplete="current-password"
                                />
                                <span class="input-group-text" id="password-addon" style="cursor: pointer;">
                                    <i class="ri-eye-off-fill"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">@translate('Remember Me')</label>
                        </div>

                        {{-- Submit --}}
                        <div class="mt-4">
                            <x-ui.button style="background: #722c81;" class="btn w-100">@translate('Login')</x-ui.button>
                        </div>
                    </form>
                </div>
            </x-ui.card-body>
        </x-ui.card>
    </x-ui.col>

<script>
    function initPasswordToggle() {
        const toggleBtn = document.getElementById('password-addon');
        const passwordInput = document.getElementById('password');

        if (!toggleBtn || !passwordInput) return;

        // Remove any existing listener to avoid duplication
        toggleBtn.onclick = null;

        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            const icon = toggleBtn.querySelector('i');
            icon.classList.toggle('ri-eye-off-fill', !isPassword);
            icon.classList.toggle('ri-eye-fill', isPassword);
        });
    }

    document.addEventListener('DOMContentLoaded', initPasswordToggle);

    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', (message, component) => {
            initPasswordToggle();
        });
    });


</script>

@if(session('from_logout'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const reloadCount = sessionStorage.getItem('loginReloadCount') || 0;

        if (reloadCount < 1) {
            sessionStorage.setItem('loginReloadCount', String(Number(reloadCount) + 1));
            window.location.reload();
        } else {
            sessionStorage.removeItem('loginReloadCount');
        }
    });
</script>
@endif



</x-ui.row>


