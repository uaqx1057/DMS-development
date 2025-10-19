<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

    <x-ui.message />
    
    <!-- Search Bar -->
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-body>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="search-box">
                                <input type="text" 
                                       class="form-control" 
                                       placeholder="Search by Platform or Business..."
                                       wire:model.live="search">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <select class="form-select w-auto" wire:model.live="perPage">
                                    <option value="10">10 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                    <option value="100">100 per page</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>

    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Platform List" :href="route('businessid.create')" :add="$add_permission"/>
                <x-ui.card-body>
                    <x-ui.table>
                        <x-ui.thead>
                            @php
                                $table_headings = ['#', 'Platform', 'Platform Id', 'Status', 'Action'];
                            @endphp
                            @foreach ($table_headings as $heading)
                                <x-ui.th :label="$heading" wire:key="{{ $loop->iteration }}"/>
                            @endforeach
                        </x-ui.thead>
                        <x-ui.tbody>
                            @forelse ($businessIds as $businessId)
                                <x-ui.tr wire:key="{{ $businessId->id }}">
                                    <x-ui.td>{{ $loop->iteration + (($businessIds->currentPage() - 1) * $businessIds->perPage()) }}</x-ui.td>
                                    <x-ui.td>{{ $businessId->business->name ?? 'N/A' }}</x-ui.td>
                                    <x-ui.td>{{ $businessId->value }}</x-ui.td>
                                    <x-ui.td>
                                        <span class="badge bg-{{ $businessId->is_active ? 'success' : 'danger' }}">
                                            {{ $businessId->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </x-ui.td>
                                    <x-ui.td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="align-middle ri-more-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <!-- Toggle Status Option -->
                                                <li>
                                                    <a href="#" 
                                                       wire:click.prevent="toggleStatus({{ $businessId->id }})" 
                                                       class="dropdown-item {{ $businessId->is_active ? 'text-warning' : 'text-success' }}">
                                                        <i class="align-bottom {{ $businessId->is_active ? 'ri-close-circle-line' : 'ri-checkbox-circle-line' }} me-2"></i> 
                                                        {{ $businessId->is_active ? 'Deactivate' : 'Activate' }}
                                                    </a>
                                                </li>
                                                
                                                @if ($edit_permission)
                                                <li>
                                                    <a href="{{ route('businessid.edit', $businessId->id) }}" wire:navigate class="dropdown-item edit-item-btn">
                                                        <i class="align-bottom ri-pencil-fill me-2 text-muted"></i> 
                                                        @translate('Edit')
                                                    </a>
                                                </li>
                                                @endif
                                                <li>
                                                    <a href="#" wire:click.prevent="deleteBusinessId({{ $businessId->id }})" 
                                                       class="dropdown-item remove-item-btn text-danger"
                                                       onclick="return confirm('Are you sure you want to delete this Platform?')">
                                                        <i class="align-bottom ri-delete-bin-fill me-2"></i> 
                                                        @translate('Delete')
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </x-ui.td>
                                </x-ui.tr>
                            @empty
                                <x-ui.tr>
                                    <x-ui.td colspan="5" class="text-center">
                                        No platforms found.
                                    </x-ui.td>
                                </x-ui.tr>
                            @endforelse
                        </x-ui.tbody>
                    </x-ui.table>

                    <!-- Pagination -->
                    @if($businessIds->hasPages())
                    <div class="mt-3">
                        {{ $businessIds->links() }}
                    </div>
                    @endif
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>
</div>