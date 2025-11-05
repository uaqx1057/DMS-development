    @props(['items', 'columns', 'page', 'perPage', 'isModalEdit' => false, 'routeEdit'=> null, 'routeView' => null, 'isModalRole' => false, 'routeRole'=> null, 'edit_permission' => false, 'showModal' => false, 'isModalView' => false])

    <tbody>
        @if ($items->isEmpty())
            <tr class="text-center">
                <td class="p-5 text-sm border" :colspan="{{ count($columns) + 1 }}">No Data Displayed.</td>
            </tr>
        @endif
        


        @foreach($items as $key => $item)
        <x-table-row
            :routeView="$routeView"
            :routeEdit="$routeEdit"
            :isModalEdit="$isModalEdit"
            :routeRole="$routeRole"
            :isModalRole="$isModalRole"
            :item="$item"
            :columns="$columns"
            :key="$key"
            :page="$page"
            :perPage="$perPage"
            :edit_permission="$edit_permission"
            :showModal="$showModal"
            :isModalView="$isModalView"
        />
        @endforeach

    </tbody>
