<x-ui.col class="col-lg-2 col-md-2 mb-3">
    <x-form.label for="gender" name="Gender"/>
    <x-form.select class="form-select" id="gender" wire:model='gender'>
        @foreach ($genders as $item)
            <x-form.option value="{{ $item->value }}" wire:key="{{ $item->value }}" :name="$item->value"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="gender"/>
</x-ui.col>
