<x-ui.col class="col-lg-3 col-md-3 mb-3">
    <x-form.label for="employment_type_id" name="Employement Type"/>
    <x-form.select class="form-select" wire:model="employment_type_id">
        <x-form.option value="" selected name="---"/>
        @foreach ($employment_types as $item)
            <x-form.option value="{{ $item->id }}" :name="$item->name"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="employment_type_id"/>
</x-ui.col>
