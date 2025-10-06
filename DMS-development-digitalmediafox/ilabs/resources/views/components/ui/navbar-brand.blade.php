<div class="navbar-brand-box">
    <!-- Dark Logo-->
    <a href="{{ route('dashboard') }}" wire:navigate class="logo logo-dark">
        <span class="logo-sm">
            <img src="{{ Vite::asset('resources/assets/images/logo-sm.png') }}g" alt="" height="22">
        </span>
        <span class="logo-lg">
            <img src="{{ Vite::asset('resources/assets/images/logo-dark.png') }}" alt="" height="17">
        </span>
    </a>
    <!-- Light Logo-->
    <a href="{{ route('dashboard') }}" wire:navigate class="logo logo-light">
        <span class="logo-sm">
            <img src="{{ Vite::asset('resources/assets/images/logo-sm.png') }}" alt="" height="22">
        </span>
        <span class="logo-lg">
            <img src="{{ Vite::asset('resources/assets/images/logo-light.png') }}" alt="" height="17">
        </span>
    </a>
    <button type="button" class="p-0 btn btn-sm fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
        <i class="ri-record-circle-line"></i>
    </button>
</div>
