<li class="nav-item">
    <a
        class="nav-link menu-link"
        href="{{ $isCollapsed ? '#'.$dataId : route($route) }}"
        @if($isCollapsed)
            data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="{{ $dataId }}"
        @else
            wire:navigate
        @endif
    >
        <i class="{{ $icon }}"></i>
        <span data-key="{{ $dataKey }}">@translate($label)</span>
    </a>

    @if ($isCollapsed)
        <div class="collapse menu-dropdown" id="{{ $dataId }}">
            <ul class="nav nav-sm flex-column">
                {{ $slot }}
            </ul>
        </div>
    @endif
</li>
