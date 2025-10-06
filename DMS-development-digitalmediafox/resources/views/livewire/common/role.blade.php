<x-ui.col class="mb-3 col-lg-3 col-md-3">
    <x-form.label for="role_id" name="User Role"/>
    <x-form.select class="form-select" wire:model='role_id'>
        @foreach ($roles as $item)
            <x-form.option value="{{ $item->id }}" :name="$item->name"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="role_id"/>
</x-ui.col>
