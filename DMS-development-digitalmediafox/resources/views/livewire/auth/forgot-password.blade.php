<x-ui.row class="justify-content-center">
    <x-ui.col class="col-md-8 col-lg-6 col-xl-5">
        <x-ui.card class="mt-4 card-bg-fill">
            <x-ui.card-body class="p-4">
                <div class="text-center mt-2">
                    <h2 class="text-primary">@translate('Forgot Password?')</h2>
                    <p class="text-muted">@translate('Reset your password here')</p>
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

                <div class="p-2 mt-4">
                    <form method="POST" action="/forgot-password">
                        @csrf

                        <div class="mb-3">
                            <x-form.label for="email" :name="@translate('Email')" />
                            <x-form.input-email name="email" placeholder="Enter email" value="{{ old('email') }}" />
                            <x-ui.alert error="email"/>
                        </div>

                        <div class="mt-4">
                            <x-ui.button class="btn-primary w-100">
                                @translate('Send Reset Link')
                            </x-ui.button>
                            
                        </div>
                        
                         <div class="mt-2">
                        <span class="text-danger">If not found in inbox, please check in spam/junk</span>

                            <div class="float-end">
                                <a href="{{ route('login') }}" class="text-muted">
                                    @translate('Login')
                                </a>
                            </div>
                         </div>    
                    </form>
                </div>
            </x-ui.card-body>
        </x-ui.card>
    </x-ui.col>
</x-ui.row>

<script>
    
    document.addEventListener('DOMContentLoaded', function () {
        // Select all alert boxes
        const alerts = document.querySelectorAll('.alert');

        alerts.forEach(function(alert) {
            setTimeout(function () {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = 0;

                // Optional: remove from DOM after fade out
                setTimeout(() => alert.remove(), 500);
            }, 5000); // 5 seconds
        });
    });


    document.addEventListener('DOMContentLoaded', function () {
        // Prevent loop
        if (performance.navigation.type === 2) {
            // User clicked back, reload fresh
            window.location.href = "{{ route('login') }}";
            return;
        }

        let redirected = false;

        // Push a dummy state to detect back
        history.pushState(null, '', location.href);

        window.addEventListener('popstate', function () {
            if (!redirected) {
                redirected = true;
                window.location.href = "{{ route('login') }}";
            }
        });
    });
</script>

