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
                    <x-ui.card-header title="Account Details"/>
                    <x-ui.card-body>
                    <x-ui.row>

                    <!-- Begin: Saltuation Card -->
                    <livewire:common.salutation wire:model="salutation"/>
                    <!-- End: Saltuation Card -->

                    <!-- Begin: Name Card -->
                    <x-ui.col class="mb-3 col-lg-3 col-md-3">
                        <x-form.label for="name" name="Employee Name" :required="true"/>
                        <x-form.input-text  id="name" :placeholder="@translate('Employee Name')" wire:model="name"/>
                        <x-ui.alert error="name"/>
                    </x-ui.col>
                    <!-- End: Name Card -->

                    <!-- Begin: Email Card -->
                    <x-ui.col class="mb-3 col-lg-4 col-md-4">
                        <x-form.label for="email" name="Employee Email" :required="true"/>
                        <x-form.input-email  :placeholder="@translate('Employee Email')" wire:model="email"/>
                        <x-ui.alert error="email"/>
                    </x-ui.col>
                    <!-- End: Email Card -->

                    <!-- Begin: Mobile Card -->
                    <x-ui.col class="mb-3 col-lg-3 col-md-3">
                        <x-form.label for="mobile" name="Mobile"/>
                        <x-form.input-text  name="mobile" :placeholder="@translate('Employee Mobile')" wire:model="mobile"/>
                    </x-ui.col>
                    <!-- End: Mobile Card -->

                    <!-- Begin: Date of Birth Card -->
                    <x-ui.col class="mb-3 col-lg-3 col-md-3">
                        <x-form.label for="dob" name="Date of Birth"/>
                        <x-form.input-date  name="dob" :placeholder="@translate('Date of Birth')"  wire:model='dob'/>
                        <x-ui.alert error="dob"/>
                    </x-ui.col>
                    <!-- End: Date of Birth Card -->

                    <!-- Begin: Gender Card -->
                    <livewire:common.gender  wire:model='gender'/>
                    <!-- End: Gender Card -->

                    <!-- Begin: Marital Status Card -->
                    <livewire:common.marital-status wire:model='marital_status'/>
                    <!-- End: Marital Status Card -->

                    <!-- Begin: Generate Password Card -->
                    <x-ui.col class="mb-3 col-lg-4 col-md-4">
                        <div class="flex justify-between mb-1">
                            <x-form.label for="name" name="Password" :required="true"/>
                            <x-ui.button type="button" class="btn btn-sm" wire:click="generatePassword">Generate Password</x-ui.button>
                        </div>
                        <x-form.input-text :placeholder="@translate('Password')" wire:model="password"/>
                        <x-ui.alert error="password"/>
                    </x-ui.col>
                    <!-- End: Generate Password Card -->


                    <!-- Begin: Image Card -->
                    <x-ui.col class="mb-3 col-lg-6 col-md-6">
                        <x-form.label for="department_id" name="Image"/>
                        <x-form.input-file accept="image/*" wire:model="image"/>
                        <x-ui.alert error="image"/>
                    </x-ui.col>

                    <x-ui.col class="mb-3 col-lg-6 col-md-6">
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" width="100px" height="100px">

                        @elseif(isset($employee->image))
                            <img src="{{  Storage::url($employee->image) }}" width="100px" height="100px">
                        @endif
                    </x-ui.col>
                    <!-- End: Image Card -->

                    <!-- Begin: Designation Card -->
                    <livewire:common.designation wire:model='designation_id'/>
                    <!-- End: Designation Card -->

                    <!-- Begin: Department Card -->
                    <livewire:common.department wire:model='department_id'/>
                    <!-- End: Department Card -->

                    <!-- Begin: Country Card -->
                    <livewire:common.country wire:model='country_id'/>
                    <!-- End: Country Card -->

                    <!-- Begin: Language Card -->
                    <livewire:common.language wire:model='language_id'/>
                    <!-- End: Language Card -->

                    <!-- Begin: Joining Card -->
                    <x-ui.col class="mb-3 col-lg-3 col-md-3">
                        <x-form.label for="joining_date" name="Joining Date"/>
                        <x-form.input-date :placeholder="@translate('Joining Date')" wire:model='joining_date'/>
                        <x-ui.alert error="joining_date"/>
                    </x-ui.col>
                    <!-- End: Joining Card -->

                    <!-- Begin: Reporting To Card -->
                    <x-ui.col class="mb-3 col-lg-3 col-md-3">
                        <x-form.label for="reporting_to" name="Reporting To"/>
                        <x-form.select class="form-select" id="reporting_to" wire:model='reporting_to'>
                            <x-form.option value="" selected name="---"/>
                            @foreach ($employees as $item)
                                <x-form.option value="{{ $item->id }}" :name="$item->name"/>
                            @endforeach
                        </x-form.select>
                        <x-ui.alert error="reporting_to"/>
                    </x-ui.col>
                    <!-- End: Reporting To Card -->

                    <!-- Begin: User Role Card -->
                    <livewire:common.role wire:model='role_id'/>
                    <!-- End: User Role Card -->

                    <!-- Begin: Status Card -->
                    <livewire:common.status wire:model='status'/>
                    <!-- End: Status Card -->

                    <!-- Begin: Address Card -->
                    <x-ui.col class="mb-3">
                        <x-form.label for="address" name="Address"/>
                        <x-form.textarea placeholder="e.g. 132, My Street, Kingston, New York 12401" wire:model='address'/>
                        <x-ui.alert error="address"/>
                    </x-ui.col>
                    <!-- End: Address Card -->

                    <!-- Begin: About Card -->
                    <x-ui.col class="mb-3">
                        <x-form.label for="about" name="About"/>
                        <x-form.textarea placeholder="" wire:model='about'/>
                        <x-ui.alert error="about"/>
                    </x-ui.col>
                    <!-- End: About Card -->

                    </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>
                <!-- End: Details Card -->

                <!-- Begin: Other Details Card -->
                <x-ui.card>
                    <x-ui.card-header title="Other Details"/>
                    <x-ui.card-body>
                    <x-ui.row>
                        <!-- Begin: Login Allowed? Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="is_login_allowed" name="Receive Email Notifcations"/>
                           <div class="flex gap-5">
                            <div class="mb-3 form-check form-radio-secondary">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="is_login_allowed"
                                    id="is_login_allowed_yes"
                                    value="1"
                                    wire:model='is_login_allowed'
                                    checked
                                />
                                <label class="form-check-label" for="is_login_allowed_yes">
                                    @translate('Yes')
                                </label>
                            </div>
                            <div class="mb-3 form-check form-radio-secondary">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="is_login_allowed"
                                    id="is_login_allowed_no"
                                    value="0"
                                    wire:model='is_login_allowed'
                                />
                                <label class="form-check-label" for="is_login_allowed_no">
                                    @translate('No')
                                </label>
                            </div>

                           </div>
                           <x-ui.alert error="is_receive_email_notification"/>
                        </x-ui.col>

                        <!-- End: Login Allowed? Card -->

                        <!-- Begin: Email Notification Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="is_receive_email_notification" name="Receive Email Notifcations"/>
                           <div class="flex gap-5">
                            <div class="mb-3 form-check form-radio-secondary">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="is_receive_email_notification"
                                    id="is_receive_email_notification_yes"
                                    value="1"
                                    wire:model='is_receive_email_notification'
                                    checked
                                />
                                <label class="form-check-label" for="is_receive_email_notification_yes">
                                    @translate('Yes')
                                </label>
                            </div>
                            <div class="mb-3 form-check form-radio-secondary">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="is_receive_email_notification"
                                    id="is_receive_email_notification_no"
                                    value="0"
                                    wire:model='is_receive_email_notification'
                                />
                                <label class="form-check-label" for="is_receive_email_notification_no">
                                    @translate('No')
                                </label>
                            </div>

                           </div>
                           <x-ui.alert error="is_receive_email_notification"/>
                        </x-ui.col>
                        <!-- End: Email Notification Card -->

                        <!-- Begin: Hourly Rate Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="hourly_rate" name="Hourly Rate" />
                            <x-form.input-number  :placeholder="@translate('Hourly Rate')" wire:model='hourly_rate'/>
                            <x-ui.alert error="hourly_rate"/>
                        </x-ui.col>
                        <!-- End: Hourly Rate Card -->

                        <!-- Begin: Slack Member ID Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="slack_member_id" name="Slack Member ID"/>
                            <x-form.input-text  :placeholder="@translate('Slack Member ID')" wire:model='slack_member_id'/>
                            <x-ui.alert error="slack_member_id"/>
                        </x-ui.col>
                        <!-- End: Slack Member ID Card -->

                        <!-- Begin: Skills Card -->
                        <x-ui.col class="mb-3 col-lg-12 col-md-12">
                            <x-form.label for="skills" name="Skills" />
                            <x-form.input-text :placeholder="@translate('Skills')" wire:model='skills'/>
                            <x-ui.alert error="skills"/>
                        </x-ui.col>
                        <!-- End: Skills Card -->

                        <!-- Begin: Probation End Date Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="probation_end_date" name="Probation End Date"/>
                            <x-form.input-date id="probation_end_date" :placeholder="@translate('Probation End Date')"  wire:model='probation_end_date'/>
                            <x-ui.alert error="probation_end_date"/>
                        </x-ui.col>
                        <!-- End: Probation End Date Card -->

                        <!-- Begin: Notice Period Start Date Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="notice_period_start_date" name="Notice Period Start Date" />
                            <x-form.input-date  id="notice_period_start_date" :placeholder="@translate('Notice Period Start Date')" wire:model='notice_period_start_date'/>
                            <x-ui.alert error="notice_period_start_date"/>
                        </x-ui.col>
                        <!-- End: Notice Period Start Date Card -->

                        <!-- Begin: Notice Period End Date Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="notice_period_end_date" name="Notice Period End Date" />
                            <x-form.input-date  id="notice_period_end_date" :placeholder="@translate('Notice Period End Date')" wire:model='notice_period_end_date'/>
                            <x-ui.alert error="notice_period_end_date"/>
                        </x-ui.col>
                        <!-- End: Notice Period End Date Card -->

                        <!-- Begin: Employement Type Card -->
                        <livewire:common.employment-type wire:model='employment_type_id'/>
                        <!-- End: Employement Type Card -->

                        <!-- Begin: Branch Card -->
                        <livewire:common.branch wire:model='branch_id'/>
                        <!-- End: Branch Card -->

                    </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>
                <!-- End: Other Details Card -->
                  <!-- end card -->
            <div class="mb-4 text-end">
                <a href="{{ route('employee.index') }}" wire:navigate class="btn btn-danger w-sm">@translate('Cancel')</a>
                <button type="submit" class="btn btn-success w-sm">@translate('Update')</button>
            </div>
            </form>


        </x-ui.col>
    </x-ui.row>
</div>
