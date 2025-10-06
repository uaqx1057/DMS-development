<div>
    <x-layouts.breadcrumb :main_menu="$main_menu" :menu="$menu"/>
    <x-ui.message />

    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-body>
                    <div class="table-responsive">
                        <form wire:submit.prevent="updatePermissions">
                            <table class="table mb-0 align-middle table-bordered table-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Module Name</th>
                                        <th>
                                            View
                                            {{-- <input type="checkbox" wire:model="selectAllView"> --}}
                                        </th>
                                        <th>
                                            Add
                                            {{-- <input type="checkbox" wire:model="selectAllAdd"> --}}
                                        </th>
                                        <th>
                                            Edit
                                            {{-- <input type="checkbox" wire:model="selectAllEdit"> --}}
                                        </th>
                                        <th>
                                            Delete
                                            {{-- <input type="checkbox" wire:model="selectAllDelete"> --}}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($modules as $index => $parent)
                                        <!-- Display Parent Module without checkboxes -->
                                        <tr>
                                            <td colspan="6" class="bg-light font-weight-bold">
                                                {{ $parent->name }}
                                            </td>
                                        </tr>

                                        <!-- Display Sub-Modules (operations) with checkboxes -->
                                        @foreach ($parent->operations as $subModule)
                                            <tr>
                                                <td>{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                                                <td>{{ $subModule->name }}</td>

                                                <!-- Conditionally display checkboxes based on permission fields -->
                                                <td>
                                                    @if($subModule->is_view)
                                                        <input type="checkbox"
                                                               wire:model="viewPermissions.{{ $subModule->id }}"
                                                               value="1">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($subModule->is_add)
                                                        <input type="checkbox"
                                                               wire:model="addPermissions.{{ $subModule->id }}"
                                                               value="1">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($subModule->is_edit)
                                                        <input type="checkbox"
                                                               wire:model="editPermissions.{{ $subModule->id }}"
                                                               value="1">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($subModule->is_delete)
                                                        <input type="checkbox"
                                                               wire:model="deletePermissions.{{ $subModule->id }}"
                                                               value="1">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>

                            <a href="{{ route('role.index') }}" navigate:true class="mt-3 btn btn-danger">Back</a>

                           @if ($role->is_default)
                            <button type="button" class="mt-3 btn btn-success">Can't Update a default Role</button>
                           @else
                            <button type="submit" class="mt-3 btn btn-success">Update Role Permission</button>
                           @endif
                        </form>
                    </div>
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>
</div>
