<div>
    <x-layouts.breadcrumb :main_menu="$main_menu" :menu="$menu" />

    <x-ui.message />

    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Penality List" :add="$add_permission" />

                <x-ui.card-body>
                    <x-table
                        :columns="$columns"
                        :page="$page"
                        :perPage="$perPage"
                        :items="$penalties"
                        :sortColumn="$sortColumn"
                        :sortDirection="$sortDirection"
                        isModalEdit="false"
                        :edit_permission="$edit_permission"
                    >
                        <x-slot name="customColumns">
                            <template x-if="column.column === 'user'">
                                <td x-text="item.user ? item.user.name : '—'"></td>
                            </template>

                            <template x-if="column.column === 'report_date'">
                                <td x-text="item.report_date ? item.report_date.split(' ')[0] : '—'"></td>
                            </template>


                            <template x-if="column.column === 'created_at'">
                                <td x-text="new Date(item.created_at).toLocaleString()"></td>
                            </template>
                        </x-slot>

                    </x-table>
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>
</div>
