@extends('layouts.driver') <!-- use your driver layout -->

@section('content')
<div class="container mt-5" style="max-width: 400px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="card-title mb-4 text-center">Driver Forgot Password</h3>

            <!-- Success message -->
            @if ($status)
                <div class="alert alert-success">{{ $status }}</div>
            @endif

            <!-- Error message -->
            @if ($error)
                <div class="alert alert-danger">{{ $error }}</div>
            @endif

            <!-- Forgot Password Form -->
            <form wire:submit.prevent="sendResetLink">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        wire:model="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        placeholder="Enter your driver email"
                    >
                    @error('email') 
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
            </form>

            <div class="mt-3 text-center">
                <a href="{{ route('driver.login') }}">Back to Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
