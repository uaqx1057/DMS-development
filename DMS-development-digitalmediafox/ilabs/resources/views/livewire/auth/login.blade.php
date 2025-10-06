<x-ui.row class="justify-content-center">
    <x-ui.col class="col-md-8 col-lg-6 col-xl-5">
        <x-ui.card class="mt-4 card-bg-fill">

            <x-ui.card-body class="p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">@translate('Welcome Back !')</h5>
                    <p class="text-muted">@translate('Sign in to continue to iLab OMS.')</p>
                </div>
                <div class="p-2 mt-4">
                    <form wire:submit="login">
                        <div class="mb-3">
                            <x-form.label for="email" :name="@translate('Email')"/>
                            <x-form.input-email id="email" wire:model="email" placeholder="Enter email"/>
                            <x-ui.alert error="email"/>
                        </div>

                        <div class="mb-3">
                            <div class="float-end">
                                <a href="auth-pass-reset-basic.html" class="text-muted">@translate('Forgot password?')</a>
                            </div>
                            <x-form.label for="password" :name="@translate('Password')"/>
                            <div class="position-relative auth-pass-inputgroup mb-3">
                                <x-form.input-password id="password" wire:model="password" placeholder="Enter password" class="pe-5 password-input"/>
                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                <x-ui.alert error="password"/>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                            <x-form.label class="form-check-label" wire:model="remember" for="auth-remember-check" :name="@translate('Remember me')"/>
                        </div>

                        <div class="mt-4">
                            <x-ui.button class="btn-success w-100">@translate('Sign In')</x-ui.button>
                        </div>
                    </form>
                </div>
            </x-ui.card-body>
            <!-- end card body -->
        </x-ui.card>
        <!-- end card -->
    </x-ui.col >
</x-ui.row>


