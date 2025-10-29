<div class="d-flex justify-content-center align-items-center vh-100 bg-light flex-column">
    <!-- Logo at top -->
    <div class="mb-4 text-center">
        <img src="{{ asset('public/logo.png') }}" alt="Logo" style="max-width: 150px;">
    </div>

    <!-- Login Card -->
    <div class="card shadow p-4" style="min-width: 350px;">
        <h4 class="mb-3 text-center fw-bold">Driver Login</h4>

        <form wire:submit.prevent="login">
            <div class="mb-3">
                <label>Iqaama Number <span class="text-danger">*</span></label>
                <input type="text" wire:model="iqaama_number" class="form-control" placeholder="Enter Iqaama Number">
                @error('iqaama_number') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Password <span class="text-danger">*</span></label>
                <input type="password" wire:model="password" class="form-control" placeholder="Enter password">
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3 text-end">
                <a href="#" class="text-decoration-none small">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
