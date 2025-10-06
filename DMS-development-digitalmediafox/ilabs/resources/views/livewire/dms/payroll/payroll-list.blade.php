<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Payroll List"/>
                <x-ui.card-body>
                    <x-table
                    :columns="$columns"
                    :page="$page" :
                    :perPage="$perPage"
                    :items="$payrolls"
                    :sortColumn="$sortColumn"
                    :sortDirection="$sortDirection"
                />
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>
</div>
