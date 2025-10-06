<x-ui.col class="mb-3 col-lg-2 col-md-2">
    <x-form.label for="type" name="Type"/>
    <x-form.select class="form-select" id="type" wire:model='type'>
        @foreach ($types as $item)
            <x-form.option value="{{ $item->value }}" wire:key="{{ $item->value }}" :name="$item->value"/>
        @endforeach
    </x-form.select>
</x-ui.col>
