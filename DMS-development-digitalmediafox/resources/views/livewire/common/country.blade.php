<x-ui.col class="col-lg-3 col-md-3 mb-3">
    <x-form.label for="country_id" name="Country"/>
    <x-form.select class="form-select" id="country_id" wire:model='country_id'>
        @foreach ($countries as $item)
            <x-form.option value="{{ $item->id }}" :name="$item->name"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="country_id"/>
</x-ui.col>
