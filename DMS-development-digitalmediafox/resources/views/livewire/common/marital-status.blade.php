<x-ui.col class="mb-3 col-lg-3 col-md-3">
    <x-form.label for="marital_status" name="Marital Status"/>
    <x-form.select class="form-select" id="marital_status" wire:model='marital_status'>
        @foreach ($marital_statuses as $item)
            <x-form.option value="{{ $item->value }}" wire:key="{{ $item->value }}" :name="$item->value"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="marital_status"/>
</x-ui.col>
