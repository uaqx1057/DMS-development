<!-- Sidebar Wrapper -->
<div id="sidebar" class="app-menu navbar-menu d-block-lg" style="background:#722c81;border-right:1px solid #722c81;" wire:ignore>
<style>
    /* Sidebar with custom scrollbar */
#sidebar {
    overflow-y: auto;
    overflow-x: hidden;
}
#sidebar::-webkit-scrollbar {
    width: 3px;
}
#sidebar::-webkit-scrollbar-thumb {
    border-radius: 3px;
}
#sidebar {
    scrollbar-width: thin;
    scrollbar-color: #decce2ff transparent;
}
</style>    
<!-- Navbar Brand -->
     
    <x-ui.navbar-brand/>
    
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
                        <x-ui.sidebar.menu
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
                        $showModule = $module->operations->some(function ($operation) use ($module) {
                            return getModulePrivilege($operation->id) || getModulePrivilege($module->id);
                        }) && $module->id !== 1;
                    @endphp

                    @if ($showModule)
                        <x-ui.sidebar.menu
                            :isCollapsed="$module->is_collapseable ?? false"
                            :dataId="$module->id"
                            :route="$module->route"
                            :icon="$module->icon"
                            :dataKey="$module->data_key"
                            :label="$module->name"
                        >
                            @foreach ($module->operations as $operation)
                                @if (getModulePrivilege($operation->id) || getModulePrivilege($module->id))
                                    
                                    @if ($operation->is_collapseable)
                                        {{-- Nested submenu (e.g., Logs under DMS) --}}
                                        <x-ui.sidebar.submenu
                                            :dataId="$operation->id"
                                            :label="$operation->name"
                                            :route="$operation->route"
                                        >
                                            @foreach ($operation->operations as $subOperation)
                                                @if (getModulePrivilege($subOperation->id) || getModulePrivilege($operation->id))
                                                    <x-ui.sidebar.menu-item
                                                        :label="$subOperation->name"
                                                        :route="$subOperation->route"
                                                        reload-page
                                                    />
                                                @endif
                                            @endforeach
                                        </x-ui.sidebar.submenu>
                                    @else
                                        {{-- Regular menu item --}}
                                        <x-ui.sidebar.menu-item
                                            :label="$operation->name"
                                            :route="$operation->route"
                                            reload-page
                                        />
                                    @endif

                                @endif
                            @endforeach
                        </x-ui.sidebar.menu>
                    @endif
                @endforeach

                <!-- Custom Menus -->
                <x-ui.sidebar.menu
                    :isCollapsed="false"
                    :dataId="999"
                    route="vehicle.index"
                    icon="ri-truck-line"
                    dataKey="vehicle"
                    label="Vehicle"
                    reload-page
                />
                <x-ui.sidebar.menu
                    :isCollapsed="false"
                    :dataId="998"
                    route="fuel.index"
                    icon="ri-gas-station-line"
                    dataKey="fuel"
                    label="Fuel"
                    reload-page
                />
                <x-ui.sidebar.menu
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

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->

<style>
/* Default sidebar */
#sidebar {
    width: 250px;
    transition: all 0.3s ease;
}
.main-content {
    margin-left: 250px;
    transition: all 0.3s ease;
}

/* Collapsed state */
#sidebar.collapsed {
    margin-left: -250px;
}
#sidebar.collapsed ~ .main-content {
    margin-left: 0;
}

/* Mobile (sidebar offcanvas) */
@media (min-width: 340px) and (max-width: 820px) {
    #sidebar {
        position: fixed;
        top: 0;
        left: -250px;
        height: 100vh;
        z-index: 1050;
        display: none;
    }

    #sidebar.active {
        left: 0;
    }

    .main-content {
        margin-left: 0 !important;
    }
}

/* Nested submenu styling */
.nav-sm .nav-sm {
    padding-left: 1rem;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("sidebarToggle");

    // Toggle sidebar
    toggleBtn.addEventListener("click", () => {
        if (window.innerWidth < 1000 ) {
            sidebar.classList.toggle("active"); // mobile
        } else {
            sidebar.classList.toggle("collapsed"); // desktop
        }
    });

    // Force full page reload on menu links
    document.querySelectorAll("#sidebar a").forEach(link => {
        link.addEventListener("click", (e) => {
            const href = link.getAttribute("href");
            if (href && !href.startsWith("#")) {
                window.location.href = href; // full reload
            }
        });
    });
});
</script>