<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />
    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit='create'>

                <!-- Begin: Business Fields Card -->
                <x-ui.card>
                    <x-ui.card-header title="Business Fields"/>
                    <x-ui.card-body>
                    <x-ui.row>
                        <!-- Begin: Name Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="name" name="Name" :required="true"/>
                            <x-form.input-text  id="name" :placeholder="@translate('Name')" wire:model="name"/>
                            <x-ui.alert error="name"/>
                        </x-ui.col>
                        <!-- End: Name Card -->

                        <!-- Begin: Type Card -->
                        <livewire:common.field-types wire:model="type"/>
                        <!-- End: Type Card -->

                        <!-- Begin: Required Card -->
                        <x-ui.col class="col-lg-1 col-md-3" style="display: flex; align-items:center">
                            <div class="form-check form-check-secondary">
                                <input class="form-check-input" type="checkbox" id="required" wire:model='required'>
                                <label class="form-check-label" for="required">
                                    @translate('Required')
                                </label>
                            </div>
                        </x-ui.col>
                        <!-- End: Required Card -->
                    </x-ui.row>

                    </x-ui.card-body>
                </x-ui.card>
                <!-- End: Business Fields Card -->

                <!-- end card -->
                <div class="mb-4 text-end">
                    <a href="{{ route('field.index') }}" wire:navigate class="btn btn-danger w-sm">@translate('Cancel')</a>
                    <button type="submit" class="btn btn-success w-sm">@translate('Create')</button>
                </div>
            </form>


        </x-ui.col>
    </x-ui.row>
</div>
