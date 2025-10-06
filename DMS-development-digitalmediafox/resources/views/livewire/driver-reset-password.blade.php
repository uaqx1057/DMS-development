<div class="reset-password">
    <h3>Driver Reset Password</h3>

    @if ($status)
        <div class="alert alert-success">{{ $status }}</div>
    @endif
    @if ($error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    <form wire:submit.prevent="resetPassword">
        <input type="email" wire:model="email" placeholder="Enter your driver email" />
        @error('email') <span class="text-danger">{{ $message }}</span> @enderror

        <input type="password" wire:model="password" placeholder="New password" />
        @error('password') <span class="text-danger">{{ $message }}</span> @enderror

        <input type="password" wire:model="password_confirmation" placeholder="Confirm password" />

        <button type="submit">Reset Password</button>
    </form>
</div>
