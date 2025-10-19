<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />
    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit='update'>
                <!-- Begin: Platform Details Card -->
                <x-ui.card>
                    <x-ui.card-header title="Platform Details"/>
                    <x-ui.card-body>
                        <x-ui.row>
                            <!-- Business Selection -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="business_id" name="Platform" :required="true"/>
                                <select class="form-control" id="business_id" wire:model="business_id">
                                    <option value="">Select Plarform</option>
                                    @foreach($businesses as $business)
                                    <option value="{{ $business->id }}">{{ $business->name }}</option>
                                    @endforeach
                                </select>
                                <x-ui.alert error="business_id"/>
                            </x-ui.col>

                            <!-- Platform Value -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="value" name="Platform Id" :required="true"/>
                                <x-form.input-text id="value" placeholder="Enter Platform Id" wire:model="value"/>
                                <x-ui.alert error="value"/>
                            </x-ui.col>


                            <!-- Status -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </x-ui.col>
                        </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>

                <!-- Action Buttons -->
                <div class="mb-4 text-end">
                    <a href="{{ route('businessid.index') }}" wire:navigate class="btn btn-danger w-sm">@translate('Cancel')</a>
                    <button type="submit" class="btn btn-success w-sm">@translate('Update')</button>
                </div>
            </form>
        </x-ui.col>
    </x-ui.row>
</div>