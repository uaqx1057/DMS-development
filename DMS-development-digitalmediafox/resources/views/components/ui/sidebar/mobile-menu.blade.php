@props([
    'isCollapsed' => false,
    'dataId',
    'route' => null,
    'icon' => null,
    'dataKey',
    'label'
])

@php
    // Check if this menu or any of its children are active
    $isActive = $route && request()->routeIs($route) || str_contains($slot, 'active-menu');
@endphp

<li class="nav-item {{ $isActive ? 'menu-open' : '' }}">
    <a
        class="nav-link menu-link text-white"
        href="{{ $isCollapsed ? '#'.$dataId : ($route ? route($route) : '#') }}"
        @if($isCollapsed)
            data-bs-toggle="collapse"
            role="button"
            aria-expanded="{{ $isActive ? 'true' : 'false' }}"
            aria-controls="{{ $dataId }}"
        @else
            wire:navigate
        @endif
    >
        <i class="{{ $icon }}"></i>
        <span data-key="{{ $dataKey }}">@translate($label)</span>
    </a>

    @if ($isCollapsed)
        <div class="collapse menu-dropdown {{ $isActive ? 'show' : '' }}" id="{{ $dataId }}">
            <ul class="nav nav-sm flex-column">
                {{ $slot }}
            </ul>
        </div>
    @endif
</li>
