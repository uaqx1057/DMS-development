
@props(['route' => null, 'label'])

@php
    $isActive = $route && request()->routeIs($route);
@endphp
@if ($label == 'Penalty Logs' || $label == 'Import CSV Logs' || $label == 'Report Logs')
    @if (auth()->user()->role_id == 1)
        <li class="nav-item">
            <a href="{{ $route ? route($route) : '#' }}"
            class="nav-link {{ $isActive ? 'active' : '' }}">
                <span>{{ $label }}</span>
            </a>
        </li>
    @endif
@elseif ($label == 'Operation Supervisor Difference Logs')
  @if (auth()->user()->role_id == 1 || auth()->user()->role->name=="cashier")
        <li class="nav-item">
            <a href="{{ $route ? route($route) : '#' }}"
            class="nav-link {{ $isActive ? 'active' : '' }}">
                <span>{{ $label }}</span>
            </a>
        </li>
    @endif
@else
    <li class="nav-item">
        <a href="{{ $route ? route($route) : '#' }}"
        class="nav-link {{ $isActive ? 'active' : '' }}">
            <span>{{ $label }}</span>
        </a>
    </li>
@endif
