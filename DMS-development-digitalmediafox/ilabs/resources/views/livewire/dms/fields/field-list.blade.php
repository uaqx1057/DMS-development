<div>
   <x-layouts.breadcrumb :main_menu="$main_menu" :menu="$menu"/>

    <x-ui.message />

    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Field List" :href="route('field.create')" :add="$add_permission"/>
                <x-ui.card-body>
                    <x-ui.table>
                        <x-ui.thead>
                            @php
                                $table_headings = ['#','Name', 'Field', 'Businesses'];
                            @endphp
                            @foreach ($table_headings as $heading)
                                <x-ui.th :label="$heading" wire:key="{{ $loop->iteration }}"/>
                            @endforeach
                        </x-ui.thead>
                        <x-ui.tbody>
                            @foreach ($fields as $field)
                                <x-ui.tr wire:key="{{ $loop->iteration }}">
                                    <x-ui.td>{{ $loop->iteration }}</x-ui.td>
                                    <x-ui.td>{{ $field->name }}</x-ui.td>
                                    <x-ui.td>
                                        <x-form.label :name="$field->name" :required="$field->required"/>
                                        @if ($field->type == 'INTEGER')
                                        <input type="number" placeholder="{{ $field->name }}" class="form-control">
                                        @elseif ($field->type == 'TEXT')
                                        <input type="text" placeholder="{{ $field->name }}" class="form-control">
                                        @elseif ($field->type == 'DOCUMENT')
                                        <input type="file" placeholder="{{ $field->name }}" class="form-control">
                                        @endif
                                    </x-ui.td>
                                    <x-ui.td>
                                        @forelse ($field->businesses as $business)
                                            <span class="border badge border-secondary text-secondary">{{ $business->name }}</span>
                                        @empty
                                            ---
                                        @endforelse
                                    </x-ui.td>
                                </x-ui.tr>
                            @endforeach
                        </x-ui.tbody>
                    </x-ui.table>
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>


</div>
