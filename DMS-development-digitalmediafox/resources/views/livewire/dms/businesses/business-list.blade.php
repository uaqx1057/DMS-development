<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Business List" :href="route('business.create')" :add="$add_permission"/>
                <x-ui.card-body>
                    <x-ui.table>
                        <x-ui.thead>
                            @php
                                $table_headings = ['#','Name', 'Branch', 'Action'];
                            @endphp
                            @foreach ($table_headings as $heading)
                                <x-ui.th :label="$heading" wire:key="{{ $loop->iteration }}"/>
                            @endforeach
                        </x-ui.thead>
                        <x-ui.tbody>
                            @foreach ($businesses as $business)
                                <x-ui.tr wire:key="{{ $loop->iteration }}">
                                    <x-ui.td>{{ $loop->iteration }}</x-ui.td>
                                    <x-ui.td>{{ $business->name }}</x-ui.td>
                                    <x-ui.td>{{ $business->branch->name ?? '' }}</x-ui.td>
                                    <x-ui.td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="align-middle ri-more-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                {{-- <li><a href="#!" class="dropdown-item"><i class="align-bottom ri-eye-fill me-2 text-muted"></i> @translate('View')</a></li> --}}
                                                @if ($edit_permission)
                                                <li><a href="{{ route('business.edit', $business->id) }}" wire:navigate class="dropdown-item edit-item-btn"><i class="align-bottom ri-pencil-fill me-2 text-muted"></i> @translate('Edit')</a></li>
                                                @endif

                                                {{-- <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="align-bottom ri-delete-bin-fill me-2 text-muted"></i> @translate('Delete')
                                                    </a>
                                                </li> --}}
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
