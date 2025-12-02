@props(['item', 'key', 'page', 'perPage', 'columns', 'isModalEdit' => false, 'isModalRole' => false, 'routeEdit'=> null, 'routeRole'=> null, 'routeView'=> null, 'edit_permission' => false, 'showModal' => false, 'isModalView' => false, 'delete_booklet' => false,'rechargeLog' => false, 'rechargePermission' => false, 'createRechargeDriver' => false])

<tr wire:key="{{ $item->id . $page }}">
    <td class="">{{ ++$key + $perPage * ($page - 1) }}.</td>
    @foreach ($columns as $column)
   
    


    <td>

        @if ($column['isData'])
       {!! $this->customFormat($column, $column['hasRelation'] ? data_get($item, $column['column'].'.'.$column['columnRelation']) : data_get($item, $column['column'])) !!}

       @elseif ($column['column'] == 'file_name')
            @if(isset($item->file_name))
                {{-- Encode the full path including directory --}}
                <a href="{{ route('download.file', ['path' => base64_encode($item->file_name)]) }}" 
                target="_blank" 
                class="text-primary text-decoration-underline">
                    {{ $item->original_name }}
                </a>
            @endif
        @elseif ($column['column'] == 'penalty_file')
            @if(isset($item->penalty_file))
                {{-- For Platform IDs Report - pass business_id_value --}}
                <a href="{{ Storage::url('app/public/' . $item->penalty_file) }}" target="_blank" class="btn btn-sm btn-primary">View Proof</a>
            @endif
        @elseif ($column['column'] == 'status')
            @if (auth()->user()->role_id == 8 || $createRechargeDriver || $rechargeLog)    
                <p class="fw-normal fs-6 badge rounded-pill 
                    {{ str_contains($item->status, 'Pending') ? 'text-bg-primary' : 
                    (str_contains($item->status, 'Accepted') ? 'text-bg-success' : 
                    (str_contains($item->status, 'Rejected') ? 'text-bg-danger' : 'text-bg-secondary')) 
                    }}">
                    {{ ucfirst($item->status) }}
                </p>
                @if (isset($item->reason) && !$rechargeLog)
                    <p>Reject Reason:</>{{ $item->reason }}</p>
                @endif
            @endif
        @elseif ($column['column'] == 'recharge')
            @if(isset($item->recharge->image))
                {{-- For Platform IDs Report - pass business_id_value --}}
                <a href="{{ Storage::url('app/public/' . $item->recharge->image) }}" target="_blank" class="btn btn-sm btn-primary">View Proof</a>
                {{-- <p>{{ $item->recharge->image }}</p> --}}
            @endif
        @elseif ($column['column'] == 'receipt_image')
            @if(isset($item->receipt_image))
                {{-- For Platform IDs Report - pass business_id_value --}}
                <a href="{{ Storage::url('app/public/' . $item->receipt_image) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
            @endif
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
                    @if ($rechargePermission)
                        @if ($item->status == 'Pending')
                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="align-middle ri-more-fill"></i>
                            </button>
                            @else
                            <div class="d-flex flex-column">
                                <div class="w-100 text-center">
                                    <p class="status-action badge fw-normal fs-6 rounded-pill 
                                        {{ str_contains($item->status, 'Pending') ? 'text-bg-warning' : 
                                        (str_contains($item->status, 'Accepted') ? 'text-bg-success' : 
                                        (str_contains($item->status, 'Rejected') ? 'text-bg-danger' : 'text-bg-secondary')) 
                                        }}">
                                        {{ ucfirst($item->status) }}
                                    </p>
                                </div>
                                @if (isset($item->reason))
                                    <p>Reject Reason: {{ $item->reason }}</p>
                                @endif
                            </div>
                        @endif
                    @else
                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="align-middle ri-more-fill"></i>
                        </button>
                    @endif
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if ($isModalView)
                            <li>
                                <a href="{{ route('drivers.view', $item['id']) }}" wire:navigate class="dropdown-item edit-item-btn">
                                    <i class="align-bottom ri-eye-fill me-2 text-muted"></i>
                                    @translate('View')
                                </a>
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

                        @if ($delete_booklet)
                            <button class="dropdown-item edit-item-btn"
                            wire:click="delete({{ $item->id }})"
                            onclick="return confirm('Are you sure you want to delete this booklet?')"
                            >
                                <i class="align-bottom ri-delete-bin-fill me-2 text-muted"></i>
                                    @translate('Delete')
                            </button>
                        @endif

                        @if ($rechargePermission)
                            @if ($item->status == 'pending')
                                <button class="dropdown-item edit-item-btn"
                                wire:click="accept({{ $item->id }})"
                                {{-- onclick="return confirm('Are you sure you want to delete this booklet?')" --}}
                                >
                                    <i class="align-bottom ri-check-fill me-2 text-success fw-bold"></i>
                                        @translate('Accept')
                                </button>
                            @endif
                        @endif
                        @if ($rechargePermission)
                            @if ($item->status == 'pending')    
                                <button class="dropdown-item edit-item-btn"
                                wire:click="reject({{ $item->id }})"
                                {{-- onclick="return confirm('Are you sure you want to delete this booklet?')" --}}
                                >
                                    <i class="align-bottom ri-close-fill me-2 text-danger fw-bold"></i>
                                        @translate('Reject')
                                </button>
                            @endif
                        @endif
                    </ul>
                </div>

            </div>
        @elseif ($column['column'] === 'driverRecharge')
            @if($item->recharge)
                <p class="badge fw-normal fs-6 rounded-pill text-bg-info">Recharged</p>
            @else
                @if ($createRechargeDriver || $rechargeLog)
                    <p class="badge rounded-pill fw-normal fs-6 {{ str_contains($item->status, 'Rejected') ? 'text-bg-danger' : 'text-bg-primary' }}">{{ str_contains($item->status, 'Rejected') ? 'Rejected' : 'Pending' }}</p>
                @else
                <a href="{{ route('recharge.create', $item->id) }}"
                wire:navigate 
                class="btn btn-sm btn-primary">
                    Recharge
                </a>
                @endif
            @endif
        @endif
    </td>
    @endforeach
</tr>
