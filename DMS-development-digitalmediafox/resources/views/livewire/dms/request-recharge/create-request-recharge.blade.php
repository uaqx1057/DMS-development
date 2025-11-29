<div>
    <x-layouts.breadcrumb :main_menu="$main_menu" :menu="$menu" />
    <x-ui.message />

    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit.prevent="save">
                <x-ui.card>
                    <x-ui.card-header title="Request Recharge" />
                    <x-ui.card-body>
                        <x-ui.row>

                            <!-- Driver -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="driver" name="Driver" :required="true" />
                               <x-form.select id="driver" wire:model="driver">
                                    <option value="">--select driver--</option>
                                    @foreach ($drivers  as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </x-form.select>
                                <x-ui.alert error="driver" />
                            </x-ui.col>

                            <!-- Begin: Mobile Card -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="phone_number" name="Phone Number" :required="true" />
                                <x-form.input-text  name="phone_number" :placeholder="@translate('Phone Number')" wire:model="phone_number"/>
                                <x-ui.alert error="phone_number" />
                            </x-ui.col>
                            <!-- End: Mobile Card -->

                            <!-- Begin: operator Card -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="operator" name="Operator" :required="true" />
                                <x-form.input-text  name="operator" :placeholder="@translate('Operator ')" wire:model="operator"/>
                                <x-ui.alert error="operator" />
                            </x-ui.col>
                            <!-- End: operator Card -->


                        </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>

                <div class="mb-4 text-end">
                    <a href="{{ route('request-recharge.index') }}" wire:navigate
                        class="btn btn-danger w-sm">@translate('Cancel')</a>
                    <button type="submit" class="btn btn-success w-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            @translate('Create')
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            @translate('creating...')
                        </span>
                    </button>
                </div>
            </form>
        </x-ui.col>
    </x-ui.row>
</div>
