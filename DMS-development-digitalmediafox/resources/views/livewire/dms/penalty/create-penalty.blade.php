<div>
    <x-layouts.breadcrumb 
    :main_menu="$main_menu"
    :menu="$menu"
     title="Add Penalty" />

    <x-ui.message />

    <form wire:submit.prevent="save">
        <div class="row">
            <x-ui.col class="col-md-4 my-2">
                <x-form.label name="Driver" />
                <x-form.select wire:model.live="driver_id">
                    <x-form.option value="" name="-- select driver --" />
                    @foreach($drivers as $driver)
                        <x-form.option :value="$driver->id" :name="$driver->name" />
                    @endforeach
                </x-form.select>
                <x-ui.alert error="driver_id" />
            </x-ui.col>

            <x-ui.col class="col-md-4 my-2">
                <x-form.label name="Platform" />
                <x-form.select wire:model.live="business_id">
                    <x-form.option value="" name="-- Select Platform --" />
                    @foreach($businesses as $b)
                        <x-form.option :value="$b->id" :name="$b->name" />
                    @endforeach
                </x-form.select>
                <x-ui.alert error="business_id" />
            </x-ui.col>

            <x-ui.col class="col-md-4 my-2">
                <x-form.label name="Platform ID" />
                <x-form.select wire:model.live="business_id_value">
                    <x-form.option value="" name="-- select id --" />
                    @foreach($businessIds as $id)
                        <x-form.option :value="$id->id" :name="$id->value" />
                    @endforeach
                </x-form.select>
                <x-ui.alert error="business_id_value" />
            </x-ui.col>

            <x-ui.col class="col-md-4 my-2">
                <x-form.label name="Penalty Date" />
                <input type="date" class="form-control" wire:model="penalty_date" max="{{ now()->format('Y-m-d') }}">
                <x-ui.alert error="penalty_date" />
            </x-ui.col>

            <x-ui.col class="col-md-4 my-2">
                <x-form.label name="Penalty Value" />
                <input type="number" class="form-control" wire:model="penalty_value" placeholder="1000">
                <x-ui.alert error="penalty_value" />
            </x-ui.col>

            <x-ui.col class="col-md-4 my-2">
                <x-form.label name="Upload File" />
                <input type="file" class="form-control" wire:model="penalty_file" accept="image/*">
                <x-ui.alert error="penalty_file" />
            </x-ui.col>

            <div class="col-12 mt-3 text-end">

            <a href="{{ route('drivers.index') }}" wire:navigate class="btn btn-danger w-sm">@translate('Cancel')</a>

            <button type="submit"
                    class="btn btn-success"
                    wire:loading.attr="disabled"
                    wire:target="save, penalty_file">
                Save Penalty <span wire:loading wire:target="save, penalty_file" class="ms-2">
                <span class="spinner-border spinner-border-sm"></span>
                
            </span>
            </button>

            

        </div>

        </div>
    </form>
</div>