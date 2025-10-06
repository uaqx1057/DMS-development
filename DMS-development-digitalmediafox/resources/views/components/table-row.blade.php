@props(['item', 'key', 'page', 'perPage', 'columns', 'isModalEdit' => false, 'isModalRole' => false, 'routeEdit'=> null, 'routeRole'=> null, 'routeView'=> null, 'edit_permission' => false, 'showModal' => false])

<tr wire:key="{{ $item->id . $page }}">
    <td class="">{{ ++$key + $perPage * ($page - 1) }}.</td>
    @foreach ($columns as $column)
   
    


    <td>

        @if ($column['isData'])
       {!! $this->customFormat($column, $column['hasRelation'] ? data_get($item, $column['column'].'.'.$column['columnRelation']) : data_get($item, $column['column'])) !!}

        @elseif ($column['column'] === 'view')
            <button class="btn btn-primary btn-sm" wire:click="openReportModal({{ $item['id'] }})">View Report</button>
        @elseif ($column['column'] === 'action')
            <div class="flex items-center justify-center gap-1">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="align-middle ri-more-fill"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
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
