@props(['item', 'key', 'page', 'perPage', 'columns', 'isModalEdit' => false, 'isModalRole' => false, 'routeEdit'=> null, 'routeRole'=> null, 'routeView'=> null, 'edit_permission' => false, 'showModal' => false, 'isModalView' => false])

<tr wire:key="{{ $item->id . $page }}">
    <td class="">{{ ++$key + $perPage * ($page - 1) }}.</td>
    @foreach ($columns as $column)
   
    


    <td>

        @if ($column['isData'])
       {!! $this->customFormat($column, $column['hasRelation'] ? data_get($item, $column['column'].'.'.$column['columnRelation']) : data_get($item, $column['column'])) !!}

        @elseif ($column['column'] === 'view')
            @if(isset($item->business_id_value))
                {{-- For Platform IDs Report - pass business_id_value --}}
                <button 
                    wire:click="openReportModal({{ $item->business_id_value }})" 
                    class="btn btn-sm btn-primary"
                    title="View Reports">
                    <i class="fas fa-eye"></i> View
                </button>
            @elseif(isset($item->id))
                {{-- For regular Coordinator Report - pass report id --}}
                <button 
                    wire:click="openReportModal({{ $item->id }})" 
                    class="btn btn-sm btn-primary"
                    title="View Report">
                    <i class="fas fa-eye"></i> View
                </button>
            @endif
        @elseif ($column['column'] === 'action')
            <div class="flex items-center justify-center gap-1">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="align-middle ri-more-fill"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if ($isModalView)
                            <li>
                                <button type="button" wire:click="openDriverView({{ $item['id'] }})" class="dropdown-item">
                                    <i class="align-bottom ri-eye-fill me-2 text-muted"></i>
                                    @translate('View')
                                </button>
                            </li>
                        @endif
                        @if ($edit_permission)
                            @if ($isModalEdit)

                                <li>
                                    <a href="{{ route($routeEdit, $item['id']) }}" wire:navigate class="dropdown-item edit-item-btn">
                                        <i class="align-bottom ri-pencil-fill me-2 text-muted"></i>
                                        @translate('Edit')
                                    </a>
                                </li>
                            @endif
                            @if ($isModalRole)
                                <li>
                                    <a href="{{ route($routeRole, $item['id']) }}" wire:navigate class="dropdown-item edit-item-btn">
                                        <i class="align-bottom ri-settings-line me-2 text-muted"></i>
                                        @translate('Role Permission Settings')
                                    </a>
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>

            </div>
        @endif
    </td>
    @endforeach
</tr>
