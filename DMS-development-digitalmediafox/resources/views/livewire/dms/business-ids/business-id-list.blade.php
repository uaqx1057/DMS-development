<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Business ID List" :href="route('businessid.create')" :add="$add_permission"/>
                <x-ui.card-body>
                    <x-ui.table>
                        <x-ui.thead>
                            @php
                                $table_headings = ['#', 'Business', 'Business ID Value', 'Status', 'Action'];
                            @endphp
                            @foreach ($table_headings as $heading)
                                <x-ui.th :label="$heading" wire:key="{{ $loop->iteration }}"/>
                            @endforeach
                        </x-ui.thead>
                        <x-ui.tbody>
                            @foreach ($businessIds as $businessId)
                                <x-ui.tr wire:key="{{ $loop->iteration }}">
                                    <x-ui.td>{{ $loop->iteration }}</x-ui.td>
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
                                                       onclick="return confirm('Are you sure you want to delete this Business ID?')">
                                                        <i class="align-bottom ri-delete-bin-fill me-2"></i> 
                                                        @translate('Delete')
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </x-ui.td>
                                </x-ui.tr>
                            @endforeach
                        </x-ui.tbody>
                    </x-ui.table>
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>
</div>