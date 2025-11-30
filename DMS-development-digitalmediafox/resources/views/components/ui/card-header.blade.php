@props([
    'href' => false,
    'title' => '',
    'add' => true,
    'search' => false, // New prop to control search visibility
    'export' => false,
    'import'=>false,
    'addMenu' => null, 
])

<div class="flex gap-2 card-header justify-content-between align-items-center">
    <h5 class="mb-0 card-title">@translate($title)</h5>

    @if ($search)
        <div class="me-3 flex-grow-1"> <!-- Adjust spacing as needed -->
            <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Search by Name or Iqaama Number" style="width:300px">
        </div>
    @endif

    <div class="d-flex gap-2">
        @if ($export)
            <button wire:click="exportPdf"
                class="btn btn-danger btn-sm"
                wire:loading.attr="disabled">
                <!-- Normal text (when not loading) -->
                <span wire:loading.remove wire:target="exportPdf">
                    <i class="ri-file-pdf-line align-bottom me-1"></i> Export PDF
                </span>
                <!-- Loading spinner -->
                <span wire:loading wire:target="exportPdf">
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Exporting...
                </span>
            </button>

           <button wire:click="exportCsv"
                class="btn btn-success"
                wire:loading.attr="disabled">
            <!-- Normal Text (when not loading) -->
            <span wire:loading.remove wire:target="exportCsv">
                <i class="align-bottom ri-file-excel-line me-1"></i> @translate('Export CSV')
            </span>
            <!-- Loading Spinner -->
            <span wire:loading wire:target="exportCsv">
                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                @translate('Exporting...')
            </span>
        </button>

        @endif
        @if ($import)
           <!-- Button to open modal -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="align-bottom ri-upload-line me-1"></i> Import CSV
            </button>

<!-- Import Modal -->



        @endif

        {{-- 🔹 Add button or custom menu --}}
        @if ($href && $add)
            @if ($addMenu)
                {{-- Dropdown menu --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="align-bottom ri-add-line me-1"></i> @translate('Add New')
                    </button>
                    <ul class="dropdown-menu">
                        @foreach ($addMenu as $item)
                            <li>
                                <a class="dropdown-item" href="{{ $item['url'] }}" wire:navigate>
                                    <i class="{{ $item['icon'] ?? 'ri-add-line' }} me-1"></i> @translate($item['label'])
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                {{-- Normal single Add button --}}
                <a href="{{ $href }}" class="btn btn-success" wire:navigate>
                    <i class="align-bottom ri-add-line me-1"></i> @translate('Add New')
                </a>
            @endif
        @endif
    </div>
</div>
