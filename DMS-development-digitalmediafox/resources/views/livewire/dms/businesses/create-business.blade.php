<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />
    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit='create'>
                <!-- Begin: Details Card -->
                <x-ui.card>
                    <x-ui.card-header title="Business Details"/>
                    <x-ui.card-body>
                    <x-ui.row>

                    <!-- Begin: Name Card -->
                    <x-ui.col class="mb-3 col-lg-6 col-md-3">
                        <x-form.label for="name" name="Name" :required="true"/>
                        <x-form.input-text  id="name" :placeholder="@translate('Name')" wire:model="name"/>
                        <x-ui.alert error="name"/>
                    </x-ui.col>
                    <!-- End: Name Card -->
                    
                     <x-ui.col class="mb-3 col-lg-6 col-md-3">
                        <x-form.label for="branch_id" name="Branch" :required="true"/>
                       <select class="form-control" name="branch_id" wire:model="branch_id">
                           <option value="">Select Branch</option>
                           @foreach($branch as $bb)
                           <option value="{{ $bb->id }}">{{ $bb->name }}</option>
                           @endforeach
                       </select>
                        <x-ui.alert error="branch_id"/>
                    </x-ui.col>

                    <x-ui.col class="mb-3 col-lg-6 col-md-3"></x-ui.col>

                       <x-ui.col class="mb-3 col-lg-12 col-md-12" style="display:inline-flex">
                    <!-- Begin: Image Card -->
                    <x-ui.col class="mb-3 col-lg-6 col-md-6">
                        
                        
                        <x-form.label for="department_id" name="Image"/>
                         <!-- File input -->
                         <x-form.input-file id="imageInput" accept="image/*" wire:model="image" />
                        <x-ui.alert error="image"/>
                        
                        
                        
                    </x-ui.col>

                    <x-ui.col class="mx-4 col-lg-6 col-md-6">
                        
                         @if($image)
                        <img src="{{ asset('storage/app/public/livewire-tmp/' . $image->getFilename()) }}"   width="100">
                    @endif
           
                    </x-ui.col>
                    <!-- End: Image Card -->
                    </x-ui.col>

                    </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>
                <!-- End: Details Card -->

                <!-- Begin: Business Fields Card -->
                <x-ui.card>
                    <x-ui.card-header title="Business Fields"/>
                    <x-ui.card-body>
                    <x-ui.row class="px-10">
                        @foreach ($fields as $field)
                            <div class="mb-3 col-3 form-check form-check-secondary">
                                <input class="form-check-input"
                                    type="checkbox"
                                    id="field_{{$field['id']}}"
                                    value="{{ $field['id'] }}"
                                    wire:model='field_ids'
                                    @if ($field['is_default'])
                                        disabled
                                    @endif
                                />
                                <label class="form-check-label" for="field_{{$field['id']}}">
                                    {{ $field['name'] }}
                                    @if ($field['type'] == 'INTEGER')
                                        <input type="number" placeholder="{{ $field['name'] }}" class="form-control" readonly>
                                    @elseif ($field['type'] == 'TEXT')
                                        <input type="text" placeholder="{{ $field['name'] }}" class="form-control" readonly>
                                    @elseif ($field['type'] == 'DOCUMENT')
                                        <input type="file" placeholder="{{ $field['name'] }}" class="form-control" readonly>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>
                <!-- End: Business Fields Card -->

                <!-- Begin: Driver Calculation Card -->
                <x-ui.card>
                    <x-ui.card-header title="Driver Calculation"/>
                    <x-ui.card-body>
                        <x-ui.row>

                            <!-- Begin: Calculation Type Selector -->
                            <x-ui.col class="mb-3 col-lg-2 col-md-2">
                                <x-form.label for="add_calculation_type" name="Calculation Type"/>
                                <x-form.select class="form-select" id="add_calculation_type" wire:model="add_calculation_type" wire:change='updateDriverRange'>
                                    @foreach ($ranges as $range)
                                        <x-form.option
                                            value="{{ $range->value }}"
                                            :name='$range->value'
                                        >
                                            {{ $range->name }}
                                        </x-form.option>
                                    @endforeach
                                </x-form.select>
                                <x-ui.alert error="add_calculation_type"/>
                            </x-ui.col>
                            <!-- End: Calculation Type Selector -->

                            <!-- Conditionally show 'From' and 'To' fields if 'Range' is selected -->
                            @if($add_calculation_type === 'RANGE')
                                <!-- Begin: Range From Card -->
                                <x-ui.col class="mb-3 col-lg-2 col-md-2">
                                    <x-form.label for="add_from" name="From" :required="true"/>
                                    <x-form.input-number id="add_from" :placeholder="@translate('From')" wire:model="add_from"/>
                                    <x-ui.alert error="add_from"/>
                                </x-ui.col>
                                <!-- End: Range From Card -->

                                <!-- Begin: Range To Card -->
                                <x-ui.col class="mb-3 col-lg-2 col-md-2">
                                    <x-form.label for="add_to" name="To" :required="true"/>
                                    <x-form.input-number id="add_to" :placeholder="@translate('To')" wire:model="add_to"/>
                                    <x-ui.alert error="add_to"/>
                                </x-ui.col>
                                <!-- End: Range To Card -->
                            @endif

                            <!-- Begin: Amount Card (Always visible, regardless of range type) -->
                            <x-ui.col class="mb-3 col-lg-2 col-md-2">
                                <x-form.label for="add_amount" name="Amount" :required="true"/>
                                <x-form.input-number id="add_amount" :placeholder="@translate('Amount')" wire:model="add_amount"/>
                                <x-ui.alert error="add_amount"/>
                            </x-ui.col>
                            <!-- End: Amount Card -->

                            <!-- Begin: Add Button -->
                            <x-ui.col class="col-lg-3 col-md-3" style="display: flex; align-items:center">
                                <x-ui.button type="button" class="btn-sm" wire:click='addDriverCalculation'>
                                    <i class="align-bottom ri-add-line me-1"></i>
                                    @translate('Add Calculation')
                                </x-ui.button>
                            </x-ui.col>
                            <!-- End: Add Button -->

                        </x-ui.row>

                        @foreach ($driver_calculations as $index => $calculation)
                        <x-ui.row>

                            <!-- Begin: Calculation Type Selector -->
                            <x-ui.col class="mb-3 col-lg-2 col-md-2">
                                <x-form.label for="calculation_type_{{ $index }}" name="Calculation Type"/>
                                <x-form.select class="form-select" id="calculation_type_{{ $index }}" wire:model="driver_calculations.{{ $index }}.calculation_type" disabled>
                                    @foreach ($ranges as $range)
                                        <x-form.option
                                            value="{{ $range->value }}"
                                            :name='$range->value'
                                            wire:key="{{ $index }}_{{ $range->value }}"
                                        >
                                            {{ $range->name }}
                                        </x-form.option>
                                    @endforeach
                                </x-form.select>
                                <x-ui.alert error="driver_calculations.{{ $index }}.calculation_type"/>
                            </x-ui.col>
                            <!-- End: Calculation Type Selector -->

                            <!-- Conditionally show 'From' and 'To' fields if 'Range' is selected -->
                            @if($calculation['calculation_type'] === 'RANGE')
                                <!-- Begin: Range From Card -->
                                <x-ui.col class="mb-3 col-lg-2 col-md-2">
                                    <x-form.label for="from_{{ $index }}" name="From" :required="true"/>
                                    <x-form.input-number id="from_{{ $index }}" :placeholder="@translate('From')" wire:model="driver_calculations.{{ $index }}.from"/>
                                    <x-ui.alert error="driver_calculations.{{ $index }}.from"/>
                                </x-ui.col>
                                <!-- End: Range From Card -->

                                <!-- Begin: Range To Card -->
                                <x-ui.col class="mb-3 col-lg-2 col-md-2">
                                    <x-form.label for="to_{{ $index }}" name="To" :required="true"/>
                                    <x-form.input-number id="to_{{ $index }}" :placeholder="@translate('To')" wire:model="driver_calculations.{{ $index }}.to"/>
                                    <x-ui.alert error="driver_calculations.{{ $index }}.to"/>
                                </x-ui.col>
                                <!-- End: Range To Card -->
                            @endif

                            <!-- Begin: Amount Card (Always visible, regardless of range type) -->
                            <x-ui.col class="mb-3 col-lg-2 col-md-2">
                                <x-form.label for="amount_{{ $index }}" name="Amount" :required="true"/>
                                <x-form.input-number id="amount_{{ $index }}" :placeholder="@translate('Amount')" wire:model="driver_calculations.{{ $index }}.amount"/>
                                <x-ui.alert error="driver_calculations.{{ $index }}.amount"/>
                            </x-ui.col>
                            <!-- End: Amount Card -->

                            <!-- Begin: DELETE Button -->
                            <x-ui.col class="col-lg-3 col-md-3" style="display: flex; align-items:center">
                                <x-ui.button type="button" class="btn-sm btn-danger" wire:click='deleteDriverCalculation({{ $index }})'>
                                    <i class="align-bottom ri-delete-bin-5-line"></i>
                                    @translate('Delete Calculation')
                                </x-ui.button>
                            </x-ui.col>
                            <!-- End: DELETE Button -->

                        </x-ui.row>
                    @endforeach


                    </x-ui.card-body>
                </x-ui.card>
                <!-- End: Driver Calculation Card -->

                <!-- end card -->
                <div class="mb-4 text-end">
                    <a href="{{ route('business.index') }}" wire:navigate class="btn btn-danger w-sm">@translate('Cancel')</a>
                    <button type="submit" class="btn btn-success w-sm">@translate('Create')</button>
                </div>
            </form>


        </x-ui.col>
    </x-ui.row>
</div>
