<div>
<style>
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #722C81;
    border: 1px solid #722C81;
}
.assigned-section {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}
.business-name-header {
    font-weight: 600;
    color: #722C81;
    margin-bottom: 10px;
    font-size: 1.1rem;
}
</style>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />
    <x-ui.message />
    
    @if(!$driverExists)
        <x-ui.row>
            <x-ui.col class="col-lg-12">
                <div class="alert alert-danger">
                    Driver not found!
                </div>
                <a href="{{ route('drivers.index') }}" wire:navigate class="btn btn-primary">@translate('Back to Drivers')</a>
            </x-ui.col>
        </x-ui.row>
    @else
        <x-ui.row>
            <x-ui.col class="col-lg-12">
                <form wire:submit.prevent='update'>
                    <!-- Begin: Details Card -->
                    <x-ui.card>
                        <x-ui.card-header title="Personal Details"/>
                        <x-ui.card-body>
                        <x-ui.row>

                        <!-- Begin: Image Card -->
                        <x-ui.col class="mb-3 col-lg-6 col-md-6">
                            <x-form.label for="department_id" name="Image"/>
                            <x-form.input-file accept="image/*" wire:model="image"/>
                            <x-ui.alert error="image"/>
                        </x-ui.col>
                        <x-ui.col class="mb-3 col-lg-6 col-md-6">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" width="100" height="100" style="object-fit: cover;">
                            @elseif($driver && $driver->image)
                                <img src="{{ Storage::url('app/public/' . $driver->image) }}" width="100px" height="100px" style="object-fit: cover;">
                            @endif
                        </x-ui.col>
                        <!-- End: Image Card -->

                        <!-- Begin: Branch Card -->
                        <livewire:common.branch wire:model='branch_id'/>
                        <!-- End: Branch Card -->

                        <!-- Begin: Name Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="name" name="Name" :required="true"/>
                            <x-form.input-text  id="name" :placeholder="@translate('Driver Name')" wire:model="name"/>
                            <x-ui.alert error="name"/>
                        </x-ui.col>
                        <!-- End: Name Card -->

                        <!-- Begin: Iqaamma Number Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="iqaama_number" name="Iqaama Number" :required="true"/>
                            <x-form.input-text  id="iqaama_number" :placeholder="@translate('Iqaama Number')" wire:model="iqaama_number"/>
                            <x-ui.alert error="iqaama_number"/>
                        </x-ui.col>
                        <!-- End: Iqaama Number Card -->

                        <!-- Begin: Iqaamma Expiry Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="iqaama_expiry" name="Iqaama Expiry" :required="true"/>
                            <x-form.input-date  id="iqaama_expiry" :placeholder="@translate('Iqaama Expiry')" wire:model="iqaama_expiry"/>
                            <x-ui.alert error="iqaama_expiry"/>
                        </x-ui.col>
                        <!-- End: Iqaama Expiry Card -->

                        <!-- Begin: Driver Type Card -->
                        <livewire:common.driver-type wire:model='driver_type_id'/>
                        <!-- End: Driver Type Card -->

                        <!-- Begin: Date of Birth Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="dob" name="Date of Birth" :required="true"/>
                            <x-form.input-date  name="dob" :placeholder="@translate('Date of Birth')"  wire:model='dob'/>
                            <x-ui.alert error="dob"/>
                        </x-ui.col>
                        <!-- End: Date of Birth Card -->

                        <!-- Begin: Absher Number Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="absher" name="Absher Number" :required="true"/>
                            <x-form.input-text  name="absher" :placeholder="@translate('Absher Number')" wire:model="absher_number"/>
                            <x-ui.alert error="absher_number"/>
                        </x-ui.col>
                        <!-- End: Absher Number Card -->

                        <!-- Begin: Sponsorship Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="sponsorship" name="Sponsorship" :required="true"/>
                            <x-form.input-text  name="sponsorship" :placeholder="@translate('Sponsorship')" wire:model="sponsorship"/>
                            <x-ui.alert error="sponsorship"/>
                        </x-ui.col>
                        <!-- End: Sponsorship Card -->

                        <!-- Begin: Sponsorship ID Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="sponsorship_id" name="Sponsorship ID" :required="true"/>
                            <x-form.input-text  name="sponsorship_id" :placeholder="@translate('Sponsorship ID')" wire:model="sponsorship_id"/>
                            <x-ui.alert error="sponsorship_id"/>
                        </x-ui.col>
                        <!-- End: Sponsorship ID Card -->

                        <!-- Begin: License Expiry Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="license_expiry" name="License Expiry" :required="true"/>
                            <x-form.input-date  name="license_expiry" :placeholder="@translate('License Expiry')"  wire:model='license_expiry'/>
                            <x-ui.alert error="license_expiry"/>
                        </x-ui.col>
                        <!-- End: License Expiry Card -->

                        <!-- Begin: Insurance Policy Number Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="insurance_policy_number" name="Insurance Policy Number" :required="true"/>
                            <x-form.input-text  name="insurance_policy_number" :placeholder="@translate('Insurance Policy Number')" wire:model="insurance_policy_number"/>
                            <x-ui.alert error="insurance_policy_number"/>
                        </x-ui.col>
                        <!-- End: Insurance Policy Number Card -->

                        <!-- Begin: Insurance Expiry Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="insurance_expiry" name="Insurance Expiry" :required="true"/>
                            <x-form.input-date  name="insurance_expiry" :placeholder="@translate('Insurance Expiry')"  wire:model='insurance_expiry'/>
                            <x-ui.alert error="insurance_expiry"/>
                        </x-ui.col>
                        <!-- End: Insurance Expiry Card -->

                        <!-- Begin: Vehicle Monthly Cost Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="vehicle_monthly_cost" name="Vehicle Monthly Cost" :required="true"/>
                            <x-form.input-number  name="vehicle_monthly_cost" :placeholder="@translate('Vehicle Monthly Cost')" wire:model="vehicle_monthly_cost"/>
                            <x-ui.alert error="vehicle_monthly_cost"/>
                        </x-ui.col>
                        <!-- End: Vehicle Monthly Cost Card -->

                        <!-- Begin: Mobile Data Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="mobile_data" name="Mobile Data" :required="true"/>
                            <x-form.input-number  name="mobile_data" :placeholder="@translate('Mobile Data')" wire:model="mobile_data"/>
                            <x-ui.alert error="mobile_data"/>
                        </x-ui.col>
                        <!-- End: Mobile Data Card -->

                        <!-- Begin: Fuel Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="fuel" name="Fuel" :required="true"/>
                            <x-form.input-number  name="fuel" :placeholder="@translate('Fuel')" wire:model="fuel"/>
                            <x-ui.alert error="fuel"/>
                        </x-ui.col>
                        <!-- End: Fuel Card -->

                        <!-- Begin: GPRS Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="gprs" name="GPRS" :required="true"/>
                            <x-form.input-number  name="gprs" :placeholder="@translate('GPRS')" wire:model="gprs"/>
                            <x-ui.alert error="gprs"/>
                        </x-ui.col>
                        <!-- End: GPRS Card -->

                        <!-- Begin: Government Levy Fee Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="government_levy_fee" name="Government Levy Fee" :required="true"/>
                            <x-form.input-number  name="government_levy_fee" :placeholder="@translate('Government Levy Fee')" wire:model="government_levy_fee"/>
                            <x-ui.alert error="government_levy_fee"/>
                        </x-ui.col>
                        <!-- End: Government Levy Fee Card -->

                        <!-- Begin: Accommodation Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="accommodation" name="Accommodation" :required="true"/>
                            <x-form.input-number  name="accommodation" :placeholder="@translate('Accommodation')" wire:model="accommodation"/>
                            <x-ui.alert error="accommodation"/>
                        </x-ui.col>
                        <!-- End: Accommodation Card -->
                        
                           <!-- Begin: nationality Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="nationality" name="Nationality" :required="true"/>
                            <x-form.input-text  name="nationality" :placeholder="@translate('Nationality')" wire:model="nationality"/>
                            <x-ui.alert error="nationality"/>
                        </x-ui.col>
                        <!-- End: nationality Card -->
                        
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                        <x-form.label for="language" name="Preferred Language" :required="true"/>
                        <x-form.select name="language" wire:model="language">
                        <option value="">@translate('Select Language')</option>
                        <option value="English">English</option>
                        <option value="Hindi">Hindi</option>
                        <option value="Urdu">Urdu</option>
                        <option value="Arabic">Arabic</option>
                        </x-form.select>
                        <x-ui.alert error="language"/>
                        </x-ui.col>
                        
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                        <x-form.label for="created_at" name="Driver Created At" :required="true"/>
                        <x-form.input-date 
                        wire:model="created_at" 
                        name="created_at"
                        type="date" 
                        />
                        <x-ui.alert error="created_at"/>
                        </x-ui.col>

        <!-- Begin: Currently Assigned Platform IDs Section -->
        <x-ui.row>
            <x-ui.col class="mb-3 col-lg-12 col-md-12">
                <x-form.label for="" name="Currently Assigned Platform IDs"/>
            </x-ui.col>
        </x-ui.row>

        <x-ui.row class="px-10">
            @if(!empty($assignedIdsByBusiness))
                @foreach($assignedIdsByBusiness as $businessId => $ids)
                    <div class="col-12 mb-3">
                        <div class="assigned-section">
                            <div class="business-name-header">
                                {{ $ids[0]->business->name }}
                            </div>
                            <div class="row">
                                @foreach($ids as $id)
                                    <div class="col-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                type="checkbox"
                                                id="assigned_id_{{ $id->id }}"
                                                value="{{ $id->id }}"
                                                wire:model="assignedBusinessIds"
                                                checked
                                            />
                                            <label class="form-check-label text-success" for="assigned_id_{{ $id->id }}">
                                                {{ $id->value }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="alert alert-info">
                        No Platform IDs currently assigned to this driver
                    </div>
                </div>
            @endif
        </x-ui.row>
        <!-- End: Currently Assigned Platform IDs Section -->

        <!-- Begin: Add New Platform IDs Button -->
        <x-ui.row class="px-10">
            <x-ui.col class="mb-3 col-lg-12">
                <button type="button" 
                        class="btn btn-primary" 
                        wire:click="toggleAddSection">
                    <i class="mdi mdi-plus-circle me-1"></i>
                    {{ $showAddSection ? 'Hide' : 'Add New Platform IDs' }}
                </button>
            </x-ui.col>
        </x-ui.row>
        <!-- End: Add New Platform IDs Button -->

        <!-- Begin: Add New Platform IDs Section (Hidden by default) -->
        @if($showAddSection)
        <x-ui.row class="px-10">
            <x-ui.col class="mb-3 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Select Platform & Available IDs</h5>
                        
                        @foreach ($businesses as $business)
                            <div class="mb-3">
                                <div class="form-check form-check-secondary">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        id="new_business_{{$business['id']}}"
                                        value="{{ $business['id'] }}"
                                        wire:model.live="business_ids"
                                    />
                                    <label class="form-check-label" for="new_business_{{$business['id']}}">
                                        {{ $business['name'] }}
                                    </label>
                                </div>
                                
                                <!-- Select2 Dropdown for Available IDs -->
                                @if(in_array($business['id'], $business_ids ?? []))
                                    <div class="mt-2" x-data="{ businessId: {{ $business['id'] }} }" x-init="
                                        $nextTick(() => {
                                            $('#new_business_ids_{{ $business['id'] }}').select2({
                                                placeholder: 'Select Available Platform IDs for {{ $business['name'] }}',
                                                allowClear: true,
                                                width: '100%'
                                            }).on('change', function() {
                                                let selected = $(this).val() || [];
                                                @this.updateNewBusinessIds({{ $business['id'] }}, selected);
                                            });
                                        });
                                    ">
                                        <select 
                                            id="new_business_ids_{{ $business['id'] }}"
                                            class="form-control select2-new-business-ids"
                                            multiple="multiple"
                                            style="width: 100%"
                                        >
                                            @if(isset($availableBusinessIds[$business['id']]))
                                                @foreach($availableBusinessIds[$business['id']] as $id)
                                                    <option value="{{ $id->id }}">
                                                        {{ $id->value }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        
                                        @if(!isset($availableBusinessIds[$business['id']]) || $availableBusinessIds[$business['id']]->count() == 0)
                                            <div class="alert alert-warning mt-2">
                                                No available (unassigned) Platform IDs for {{ $business['name'] }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-ui.col>
        </x-ui.row>


        @endif
        @push('scripts')
<script>
document.addEventListener('livewire:navigated', () => {
    // Function to initialize all select2 dropdowns
    function initSelect2() {
        $('.select2-new-business-ids').each(function() {
            let $el = $(this);
            if (!$el.hasClass("select2-hidden-accessible")) {
                let businessId = $el.attr('id').replace('new_business_ids_', '');
                $el.select2({
                    placeholder: 'Select Available Platform IDs',
                    allowClear: true,
                    width: '100%'
                }).on('change', function () {
                    let selected = $(this).val() || [];
                    @this.updateNewBusinessIds(parseInt(businessId), selected);
                });
            }
        });
    }

    // Initialize when Livewire first loads
    initSelect2();

    // Reinitialize after DOM updates
    Livewire.hook('morph.updated', (context) => {
        initSelect2();
    });

    // Also handle custom Livewire event if needed
    Livewire.on('reinit-select2', () => {
        initSelect2();
    });
});
</script>
@endpush

        <!-- End: Add New Platform IDs Section -->

        <x-ui.alert error="business_ids"/>
        <x-ui.alert error="assignedBusinessIds"/>

                        <!-- Begin: Remarks Card -->
                        <x-ui.col class="mb-3">
                            <x-form.label for="remarks" name="Remarks"/>
                            <x-form.textarea placeholder="Remarks" wire:model='remarks'/>
                            <x-ui.alert error="Remarks"/>
                        </x-ui.col>
                        <!-- End: Remarks Card -->

                        </x-ui.row>
                        </x-ui.card-body>
                    </x-ui.card>
                    <!-- End: Details Card -->

                    <!-- Begin: Other Details Card -->
                    <x-ui.card>
                        <x-ui.card-header title="Contact"/>
                        <x-ui.card-body>
                        <x-ui.row>

                             <!-- Begin: Email Card -->
                        <x-ui.col class="mb-3 col-lg-6 col-md-6">
                            <x-form.label for="email" name="Email" :required="true"/>
                            <x-form.input-email  :placeholder="@translate('Email')" wire:model="email"/>
                            <x-ui.alert error="email"/>
                        </x-ui.col>
                        <!-- End: Email Card -->

                        <!-- Begin: Mobile Card -->
                        <x-ui.col class="mb-3 col-lg-6 col-md-6">
                            <x-form.label for="mobile" name="Mobile" :required="true"/>
                            <x-form.input-text  name="mobile" :placeholder="@translate('Mobile')" wire:model="mobile"/>
                            <x-ui.alert error="mobile"/>
                        </x-ui.col>
                        <!-- End: Mobile Card -->

                        </x-ui.row>
                        </x-ui.card-body>
                    </x-ui.card>
                    <!-- End: Other Details Card -->
                      <!-- end card -->
                <div class="mb-4 text-end">
                    <a href="{{ route('drivers.index') }}" wire:navigate class="btn btn-danger w-sm">@translate('Cancel')</a>
                    <button type="submit" class="btn btn-success w-sm">@translate('Update')</button>
                </div>
                </form>
            </x-ui.col>
        </x-ui.row>
    @endif
</div>