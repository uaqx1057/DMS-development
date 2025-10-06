<thead>
    <tr>
        <th>No.
        </th>
        @foreach($columns as $key => $value)
        @if($value['isData'])
        <th  wire:click="doSort('{{ $value['column'] }}')">
            <x-datatable-column  :sortColumn="$sortColumn" :sortDirection="$sortDirection" columnName="{{ $value['label'] }}" />
        </th>
        @else
        <th style="width: 10%;" class="text-center">{{ $value['label'] }}</th>
        @endif
        @endforeach
    </tr>
</thead>
