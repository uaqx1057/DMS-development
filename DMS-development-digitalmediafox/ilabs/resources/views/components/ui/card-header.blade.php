@props([
    'href' => false,
    'title' => '',
    'add' => true,
    'search' => false // New prop to control search visibility
])

<div class="flex gap-2 card-header justify-content-between align-items-center">
    <h5 class="mb-0 card-title">@translate($title)</h5>

    @if ($search)
        <div class="me-3 flex-grow-1"> <!-- Adjust spacing as needed -->
            <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Search by Name or Iqaama Number" style="width:300px">
        </div>
    @endif

    @if ($href && $add)
        <a href="{{ $href }}" class="btn btn-success" wire:navigate><i class="align-bottom ri-add-line me-1"></i> @translate('Add New')</a>
    @endif
</div>
