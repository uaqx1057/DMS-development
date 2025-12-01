<div>

    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Recharge Logs" :href="route('request-recharge.create')" :add="$add_permission" :export="$requestRecharges->count() > 0" />
                <x-ui.card-body>
                    <x-table
                        :columns="$columns"
                        :page="$page"
                        :perPage="$perPage"
                        :items="$requestRecharges"
                        :sortColumn="$sortColumn"
                        :sortDirection="$sortDirection"
                        isModalEdit="true"
                        routeEdit="booklet.edit"
                        :edit_permission="$edit_permission"
                        :rechargePermission="true"
                        :rechargeLog="true"
                    />
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>

</div>
