
@props(['route' => null, 'label'])

@php
    $isActive = $route && request()->routeIs($route);
@endphp

<li class="nav-item">
    <a href="{{ $route ? route($route) : '#' }}"
       class="nav-link {{ $isActive ? 'active' : '' }}">
        <span>{{ $label }}</span>
    </a>
</li>
