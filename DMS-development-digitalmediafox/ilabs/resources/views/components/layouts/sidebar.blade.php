<div class="app-menu navbar-menu">
    <!-- Begin: Navbar Brand -->
    <x-ui.navbar-brand/>
    <!-- End: Navbar Brand -->

    <!-- Begin: Sidebar Menu -->
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <!-- Begin: Separator -->
                <x-ui.sidebar.seprater name="Menu"/>
                <!-- End: Separator -->

                <!-- Begin: Module Listing -->
                @php
                    $modules = getModules();
                @endphp


                @foreach ($modules as $module)
                    {{-- Show Dashboard Seprately --}}
                    @php
                        // Check if the module is Dashboard (id === 1) and if any of its operations has 'is_view' permission
                        $showDashboard = $module->id === 1 && $module->operations->some(fn($operation) => getModulePrivilege($operation->id) && $operation->is_view === 1);
                    @endphp



                    @if ($showDashboard)
                        <x-ui.sidebar.menu
                            :isCollapsed="$module->is_collapseable ?? false"
                            :dataId="$module->id"
                            :route="$module->route"
                            :icon="$module->icon"
                            :dataKey="$module->data_key"
                            :label="$module->name"
                        >
                        </x-ui.sidebar.menu>
                    @endif

                         
                      


                    {{-- Show Other Menus Without Dashboard --}}

                    @php
                    // Check if the module has at least one operation with is_view permission
                    $showModule = $module->operations->some(function ($operation) {
                        $checkOperationPermission = getModulePrivilege($operation->id);
                        return $checkOperationPermission && $operation->is_view === 1;
                    }) && $module->id !== 1;
                @endphp

                @if ($showModule)


                    <!-- Collapsible Module Menu -->
                    <x-ui.sidebar.menu
                        :isCollapsed="$module->is_collapseable ?? false"
                        :dataId="$module->id"
                        :route="$module->route"
                        :icon="$module->icon"
                        :dataKey="$module->data_key"
                        :label="$module->name"
                    >
                        @foreach ($module->operations as $operation)
                            @php
                                $checkOperationPermission = getModulePrivilege($operation->id);
                            @endphp

                            <!-- Operation Menu Item -->
                            @if ($checkOperationPermission && $operation->is_view === 1)
                                <x-ui.sidebar.menu-item
                                    :label="$operation->name"
                                    :route="$operation->route"
                                />
                            @endif
                        @endforeach
                    </x-ui.sidebar.menu>
                @endif

                @endforeach
                <!-- End: Module Listing -->
                <!-- Custom Menu: Vehicle -->
                        <x-ui.sidebar.menu
                            :isCollapsed="false"
                            :dataId="999"
                            route="vehicle.index"
                            icon="ri-car-line"
                            dataKey="vehicle"
                            label="Vehicle"
                        />


            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <!-- End: Sidebar Menu -->

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
