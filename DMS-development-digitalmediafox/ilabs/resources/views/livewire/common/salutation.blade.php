<x-ui.col class="col-lg-2 col-md-2 mb-3">
    <x-form.label for="salutation" name="Salutation"/>
    <x-form.select class="form-select" wire:model="salutation">
        <x-form.option value selected name="---"/>
        @foreach ($salutations as $item)
            <x-form.option value="{{ $item->value }}" :name="$item->value"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="salutation"/>
</x-ui.col>

