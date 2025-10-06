<!-- Sidebar Wrapper -->
 <div id="mobile-sidebar" class="mobile-menu navbar-menu" style="display:none;background:#722c81;border-right:1px solid #722c81;">
<style>
      @media (min-width:340px) and (max-width:800px) {
    #mobile-sidebar {
        display: block !important;
        position: fixed;
        top: 0;
        left: -250px;
        width: 160px;
        height: -webkit-fill-available;
        background: #722c81;
        z-index: 1050;
        transition: left 0.3s ease;
    }

    #mobile-sidebar.active {
        left: 0;
    }

    #mobile-sidebar-overlay {
        display: none;
    }

    #mobile-sidebar-overlay.active {
        display: block !important;
        position: fixed;
        top: 0;
        left: 0;
        width: -webkit-fill-available;
        height: -webkit-fill-available;
        background: rgba(0,0,0,0.4);
        z-index: 1040;
    }

    .main-content {
        margin-left: 0 !important;
    }
}
</style>    
    <div class="bg-white text-center">
    <a href="{{ route('dashboard') }}" wire:navigate class="logo-light">
       
        <span class="logo-lg">
            <img src="{{ asset('public/logo.png') }}" alt="" width="160px">
        </span>
    </a>
    <button type="button" class="p-0 btn btn-sm fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
        <i class="ri-record-circle-line"></i>
    </button>
</div>

    <!-- Sidebar Menu -->
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
           <ul class="navbar-nav" id="navbar-nav">

                <!-- Separator -->
                <x-ui.sidebar.seprater name="Menu"/>

                <!-- Dynamic Modules -->
                @php $modules = getModules(); @endphp
                @foreach ($modules as $module)
                    @php
                        $showDashboard = $module->id === 1 && $module->operations->some(
                            fn($operation) => getModulePrivilege($operation->id) && $operation->is_view === 1
                        );
                    @endphp

                    @if ($showDashboard)
                        <x-ui.sidebar.mobile-menu
                            :isCollapsed="$module->is_collapseable ?? false"
                            :dataId="$module->id"
                            :route="$module->route"
                            :icon="$module->icon"
                            :dataKey="$module->data_key"
                            :label="$module->name"
                            reload-page
                        />
                    @endif

                    @php
                        $showModule = $module->operations->some(function ($operation) {
                            return getModulePrivilege($operation->id) && $operation->is_view === 1;
                        }) && $module->id !== 1;
                    @endphp

                    @if ($showModule)
                        <x-ui.sidebar.mobile-menu
                            :isCollapsed="$module->is_collapseable ?? false"
                            :dataId="$module->id"
                            :route="$module->route"
                            :icon="$module->icon"
                            :dataKey="$module->data_key"
                            :label="$module->name"
                        >
                            @foreach ($module->operations as $operation)
                                @if (getModulePrivilege($operation->id) && $operation->is_view === 1)
                                    <x-ui.sidebar.mobile-menu-item
                                        :label="$operation->name"
                                        :route="$operation->route"
                                        reload-page
                                    />
                                @endif
                            @endforeach
                        </x-ui.sidebar.menu>
                    @endif
                @endforeach

                <!-- Custom Menus -->
                <x-ui.sidebar.mobile-menu
                    :isCollapsed="false"
                    :dataId="999"
                    route="vehicle.index"
                    icon="ri-truck-line"
                    dataKey="vehicle"
                    label="Vehicle"
                    reload-page
                />
                <x-ui.sidebar.mobile-menu
                    :isCollapsed="false"
                    :dataId="998"
                    route="fuel.index"
                    icon="ri-gas-station-line"
                    dataKey="fuel"
                    label="Fuel"
                    reload-page
                />
                <x-ui.sidebar.mobile-menu
                    :isCollapsed="false"
                    :dataId="997"
                    route="orders.index"
                    icon="ri-shopping-cart-2-line"
                    dataKey="order"
                    label="Order"
                    reload-page
                />
            </ul>
        </div>
    </div>

   
</div>
 <div id="mobile-sidebar-overlay" style="display:none;"></div>
<!-- Left Sidebar End -->

