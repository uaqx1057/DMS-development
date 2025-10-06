@props([
    'href' => false,
    'title' => '',
    'add' => true,
    'search' => false, // New prop to control search visibility
     'export' => false,
     'import'=>false,
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
            <button wire:click="exportPdf" class="btn btn-danger">
                <i class="align-bottom ri-download-line me-1"></i> @translate('Export PDF')
            </button>
        @endif
          @if ($import)
           <!-- Button to open modal -->
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
   <i class="align-bottom ri-upload-line me-1"></i> Import CSV
</button>

<!-- Import Modal -->



        @endif

        @if ($href && $add)
            <a href="{{ $href }}" class="btn btn-success" wire:navigate>
                <i class="align-bottom ri-add-line me-1"></i> @translate('Add New')
            </a>
        @endif
    </div>
</div>
