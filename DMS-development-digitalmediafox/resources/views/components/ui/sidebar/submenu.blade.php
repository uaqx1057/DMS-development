@props([
    'dataId',
    'route' => null,
    'label'
])

@php
    // Check if this submenu or any of its children are active
    $isActive = $route && request()->routeIs($route) || str_contains($slot, 'active');
@endphp

<li class="nav-item">
    <a
        class="nav-link {{ $isActive ? 'active' : '' }}"
        href="#submenu-{{ $dataId }}"
        data-bs-toggle="collapse"
        role="button"
        aria-expanded="{{ $isActive ? 'true' : 'false' }}"
        aria-controls="submenu-{{ $dataId }}"
    >
        <span>{{ $label }}</span>
    </a>

    <div class="collapse menu-dropdown {{ $isActive ? 'show' : '' }}" id="submenu-{{ $dataId }}">
        <ul class="nav nav-sm flex-column">
            {{ $slot }}
        </ul>
    </div>
</li>