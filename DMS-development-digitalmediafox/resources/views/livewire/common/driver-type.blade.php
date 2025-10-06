<x-ui.col class="col-lg-3 col-md-3 mb-3">
    <x-form.label for="driver_type_id" name="Driver Type" :required="true"/>
    <x-form.select id="driver_type_id" wire:model="driver_type_id">
        @foreach ($driver_types as $item)
            <x-form.option value="{{ $item->id }}" :name="$item->name"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="driver_type_id"/>
</x-ui.col>
