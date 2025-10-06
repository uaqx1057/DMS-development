<x-ui.col class="col-lg-3 col-md-3 mb-3">
    <x-form.label for="designation_id" name="Designation" :required="true"/>
    <x-form.select id="designation_id" wire:model="designation_id">
        <x-form.option value name="---"/>
        @foreach ($designations as $item)
            <x-form.option value="{{ $item->id }}" :name="$item->name"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="designation_id"/>
</x-ui.col>
