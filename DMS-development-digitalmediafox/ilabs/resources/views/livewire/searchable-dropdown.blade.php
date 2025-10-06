<div>
    <input
        type="text"
        wire:model.debounce.300ms="search"
        class="mb-2 form-control"
        placeholder="Search..."/>

    <select class="form-select" wire:model="selected">
        <option value="">--select driver--</option>
        @foreach ($filteredOptions as $option)
            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
        @endforeach
    </select>
</div>
