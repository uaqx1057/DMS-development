<x-ui.col class="col-lg-3 col-md-3 mb-3">
    <x-form.label for="department_id" name="Department" :required='true'/>
    <x-form.select wire:model='department_id'>
        <x-form.option value name="---"/>
        @foreach ($departments as $item)
            <x-form.option value="{{ $item->id }}" :name="$item->name"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="department_id"/>
</x-ui.col>
