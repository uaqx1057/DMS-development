
@props(['route' => null, 'label', 'moduleId'])

@php
    $isActive = $route && request()->routeIs($route);
    $accessModule = accessModule($moduleId);
@endphp

@if(isset($accessModule) && $accessModule->is_view == 1)
    <li class="nav-item">
        <a href="{{ $route ? route($route) : '#' }}"
        class="nav-link {{ $isActive ? 'active' : '' }}">
            <span>{{ $label}}</span>
        </a>
    </li>
@endif