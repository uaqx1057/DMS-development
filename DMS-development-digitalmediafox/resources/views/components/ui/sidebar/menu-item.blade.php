
@props(['route' => null, 'label', 'moduleId'])

@php
    $isActive = $route && request()->routeIs($route);
    $accessModule = accessModule($moduleId);
    $labelName = $label;
    if($label == 'Recharge Request'){
        if(in_array(auth()->user()->role_id,[1,8])){
            $labelName = 'Request Recharge';
        }else{
            $labelName = $label;
        }
    }

    if($label == 'Recharge'){
        if(in_array(auth()->user()->role_id,[1,12])){
            $labelName = 'Recharge Request';
        }else{
            $labelName = $label;
        }
    }


@endphp

@if(isset($accessModule) && $accessModule->is_view == 1)
    <li class="nav-item">
        <a href="{{ $route ? route($route) : '#' }}"
        class="nav-link {{ $isActive ? 'active' : '' }}">
            <span>{{ $labelName}}</span>
        </a>
    </li>
@endif