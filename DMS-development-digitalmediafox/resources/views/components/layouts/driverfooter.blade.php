<footer class="bg-white shadow-lg border-t fixed bottom-0 left-0 z-50 footerBorder">
    <ul class="ful flex justify-around items-center py-2 text-xs text-gray-700 text-center">
        
      <a href="/driver/dashboard" class="text-decoration-none text-dark">
    <li class="grid flex-col items-center justify-center {{ request()->is('driver/dashboard') ? 'activeli' : '' }}">
        <i class="ri-home-4-line fs-3"></i>
        <span>Home</span>
    </li>
</a>

<a href="/driver/order" class="text-decoration-none text-dark">
    <li class="grid flex-col items-center justify-center {{ request()->is('driver/order*') ? 'activeli' : '' }}">
        <i class="ri-map-pin-line fs-3"></i>
        <span>Orders</span>
    </li>
</a>

<a href="/driver/fuel" class="text-decoration-none text-dark">
    <li class="grid flex-col items-center justify-center {{ request()->is('driver/fuel*') ? 'activeli' : '' }}">
        <i class="ri-gas-station-line fs-3"></i>
        <span>Fuel</span>
    </li>
</a>

<a href="/driver/vehicle" class="text-decoration-none text-dark">
    <li class="grid flex-col items-center justify-center {{ request()->is('driver/vehicle*') ? 'activeli' : '' }}">
        <i class="ri-truck-line fs-3"></i>
        <span>Vehicle</span>
    </li>
</a>

<a href="/driver/profile" class="text-decoration-none text-dark">
    <li class="grid flex-col items-center justify-center {{ request()->is('driver/profile*') ? 'activeli' : '' }}">
        <i class="ri-user-line fs-3"></i>
        <span>Profile</span>
    </li>
</a>

        
    </ul>
    
</footer>
