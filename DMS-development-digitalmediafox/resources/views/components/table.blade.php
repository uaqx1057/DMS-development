@props([
    'sortColumn',
    'items',
    'columns',
    'page',
    'perPage',
    'sortDirection',
    'isModalEdit' => false,
    'routeEdit' => null,
    'routeView' => null,
    'isModalRole' => false,
    'routeRole'=> null,
    'edit_permission' => false,
    'showModal' => false,
    'isModalView' => false
])

<div class="card">
    <div class="py-3 card-body border-bottom">
        <div class="d-flex align-items-center">
            <div class="text-muted">
                Show
                <div class="mx-2 d-inline-flex">
                    <p class="mb-0">{{ $perPage }}</p>
                </div>
                entries
            </div>
            <div class="ms-auto text-muted">
                Search:
                <div class="ms-2 d-inline-block">
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control form-control-sm"
                        aria-label="Search">
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap">
            <x-table-head
                :columns="$columns"
                :sortColumn="$sortColumn"
                :sortDirection="$sortDirection"
            />
            <x-table-body
                :isModalEdit="$isModalEdit"
                :isModalRole="$isModalRole"
                :routeEdit="$routeEdit"
                :routeRole="$routeRole"
                :routeView="$routeView"
                :items="$items"
                :columns="$columns"
                :page="$page"
                :perPage="$perPage"
                :edit_permission="$edit_permission"
                :showModal="$showModal"
                :isModalView="$isModalView"
            />
        </table>
    </div>
    <div class="card-footer d-flex align-items-center justify-content-between">
        <div class="gap-2 d-flex align-items-center">
            <label for="perPage" class="mb-0">Per Page</label>
            <select class="w-auto form-select form-select-sm" wire:model.live="perPage" id="perPage">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
        </div>
        <div>
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
