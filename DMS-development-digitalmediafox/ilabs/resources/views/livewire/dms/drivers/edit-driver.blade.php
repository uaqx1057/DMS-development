<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />
    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit='update'>
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
                            <img src="{{ $image->temporaryUrl() }}" width="100px" height="100px">

                        @elseif($driver->image)
                            <img src="{{  Storage::url($driver->image) }}" width="100px" height="100px">
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

                    <x-ui.row>
                        <x-ui.col class="mb-3 col-lg-12 col-md-12">
                            <x-form.label for="" name="Select Business"/>
                        </x-ui.col>
                    </x-ui.row>
                    <x-ui.row class="px-10">
                           <!-- Begin: Business Card -->
                           @foreach ($businesses as $business)
                                <div class="mb-3 col-2 form-check form-check-secondary">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        id="business_{{$business['id']}}"
                                        value="{{ $business['id'] }}"
                                        wire:model="business_ids"
                                        wire:change="handleBusinessSelection"
                                    />
                                    <label class="form-check-label" for="business_{{$business['id']}}">
                                        {{ $business['name'] }}
                                    </label>
                                </div>
                            @endforeach
                            <x-ui.alert error="business_ids"/>
                        <!-- End: Business Card -->
                    </x-ui.row>

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
</div>
