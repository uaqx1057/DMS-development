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
                    <x-ui.card-header title="Role"/>
                    <x-ui.card-body>
                    <x-ui.row>
                        <!-- Begin: Name Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="name" name="Name" :required="true"/>
                            <x-form.input-text  id="name" :placeholder="@translate('Name')" wire:model="name"/>
                            <x-ui.alert error="name"/>
                        </x-ui.col>
                        <!-- End: Name Card -->

                    </x-ui.row>

                    </x-ui.card-body>
                </x-ui.card>
                <!-- End: Business Fields Card -->

                <!-- end card -->
                <div class="mb-4 text-end">
                    <a href="{{ route('role.index') }}" wire:navigate class="btn btn-danger w-sm">@translate('Cancel')</a>
                    <button type="submit" class="btn btn-success w-sm">@translate('Create')</button>
                </div>
            </form>


        </x-ui.col>
    </x-ui.row>
</div>
