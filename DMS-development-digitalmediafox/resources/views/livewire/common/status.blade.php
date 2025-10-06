<x-ui.col class="mb-3 col-lg-3 col-md-3">
    <x-form.label for="status" name="Status"/>
    <x-form.select class="form-select" wire:model='status'>
        @foreach ($statuses as $item)
            <x-form.option value="{{ $item->name }}" :name="$item->name"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="status"/>
</x-ui.col>
