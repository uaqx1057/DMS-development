<div>
<style>
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #722C81;
    border: 1px solid #722C81;
}
</style>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />
    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit.prevent="create">
                <!-- Begin: Details Card -->
                <x-ui.card>
                    <x-ui.card-header title="Personal Details"/>
                    <x-ui.card-body>
                    <x-ui.row>

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
                        <img src="{{ asset('storage/app/public/livewire-tmp/' . $image->getFilename()) }}" width="100">
                    @endif
                </x-ui.col>
                    
                    <!-- End: Image Card -->
                    </x-ui.col>

                    <!-- Begin: Branch Card -->
                    <livewire:common.branch wire:model='branch_id' :selected="true"/>
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




                    <!-- Business Selection with Dynamic Select2 Dropdowns -->
<x-ui.row>
    <x-ui.col class="mb-3 col-lg-12 col-md-12">
        <x-form.label for="" name="Select Plaform & IDs"/>
    </x-ui.col>
</x-ui.row>
<x-ui.row class="px-10">
    @foreach ($businesses as $business)
        <div class="mb-3 col-6">
            <div class="form-check form-check-secondary">
                <input class="form-check-input"
                    type="checkbox"
                    id="business_{{$business['id']}}"
                    value="{{ $business['id'] }}"
                    wire:model.live="business_ids"
                />
                <label class="form-check-label" for="business_{{$business['id']}}">
                    {{ $business['name'] }}
                </label>
            </div>
            
            <!-- Select2 Dropdown - Shows only when checkbox is checked -->
            @if(in_array($business['id'], $business_ids ?? []))
                <div class="mt-2" x-data="{ businessId: {{ $business['id'] }} }" x-init="
                    $nextTick(() => {
                        $('#business_ids_{{ $business['id'] }}').select2({
                            placeholder: 'Select Platform IDs for {{ $business['name'] }}',
                            allowClear: true,
                            width: '100%'
                        }).on('change', function() {
                            let selected = $(this).val() || [];
                            @this.updateSelectedBusinessIds({{ $business['id'] }}, selected);
                        });
                    });
                ">
                    <select 
                        id="business_ids_{{ $business['id'] }}"
                        class="form-control select2-business-ids"
                        multiple="multiple"
                        style="width: 100%"
                    >
                        @if(isset($availableBusinessIds[$business['id']]))
                            @foreach($availableBusinessIds[$business['id']] as $id)
                                @php
                                    $currentDriver = $id->currentDriver();
                                    $isAssigned = !is_null($currentDriver);
                                @endphp
                                <option 
                                    value="{{ $id->id }}"
                                    @if(in_array($id->id, $selectedBusinessIds[$business['id']] ?? []))
                                        selected
                                    @endif
                                >
                                    {{ $id->value }}
                                    @if($isAssigned)
                                        (Assigned to: {{ $currentDriver->name }})
                                    @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            @endif
        </div>
    @endforeach
    <x-ui.alert error="business_ids"/>
</x-ui.row>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.updated', ({ el, component }) => {
            // Reinitialize Select2 after Livewire updates
            $('.select2-business-ids').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    let businessId = $(this).attr('id').replace('business_ids_', '');
                    $(this).select2({
                        placeholder: 'Select Platform IDs',
                        allowClear: true,
                        width: '100%'
                    }).on('change', function() {
                        let selected = $(this).val() || [];
                        @this.updateSelectedBusinessIds(parseInt(businessId), selected);
                    });
                }
            });
        });
    });
</script>
@endpush

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
                <button type="submit" class="btn btn-success w-sm">@translate('Create')</button>
            </div>
            </form>


        </x-ui.col>


    </x-ui.row>
</div>
