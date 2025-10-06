<x-ui.col class="col-lg-3 col-md-3 mb-3">
    <x-form.label for="language_id" name="Language"/>
    <x-form.select class="form-select" id="language_id" wire:model='language_id'>
        @foreach ($languages as $item)
            <x-form.option value="{{ $item->id }}" :name="$item->name"/>
        @endforeach
    </x-form.select>
    <x-ui.alert error="language_id"/>
</x-ui.col>
