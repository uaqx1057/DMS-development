<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />
    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Driver Type List" :href="route('driver-types.create')" :add="$add_permission"/>
                <x-ui.card-body>
                    <x-table
                    :columns="$columns"
                    :page="$page"
                    :perPage="$perPage"
                    :items="$driverTypes"
                    :sortColumn="$sortColumn"
                    :sortDirection="$sortDirection"
                    isModalEdit="true"
                    routeEdit="driver-types.edit"
                    :edit_permission="$edit_permission"
                />
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>
</div>
