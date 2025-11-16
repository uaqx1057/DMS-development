<div>
    <x-layouts.breadcrumb :main_menu="$main_menu" :menu="$menu" />
    <x-ui.message />

    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit.prevent="save">
                <x-ui.card>
                    <x-ui.card-header title="Driver Receipt Form" />
                    <x-ui.card-body>
                        <x-ui.row>

                            <!-- Booklet Number -->

                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="booklet_number" name="Booklet Number" :required="true" />
                               <x-form.select id="booklet_number" wire:model="booklet_number">
                                    <option value="">--select booklet--</option>
                                    @foreach ($booklets as $booklet)
                                            <option value="{{ $booklet->id }}">{{ $booklet->booklet_number }}</option>
                                    @endforeach
                                </x-form.select>
                                <x-ui.alert error="booklet_number" />
                            </x-ui.col>

                            <!-- Receipt No -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="reaceipt_no" name="Receipt No" :required="true" />
                                <x-form.input-text id="reaceipt_no" :placeholder="@translate('Enter Receipt No')" wire:model="reaceipt_no" />
                                <x-ui.alert error="reaceipt_no" />
                            </x-ui.col>

                            <!-- Receipt Date -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="receipt_date" name="Receipt Date" :required="true" />
                                <x-form.input-date 
                                    id="receipt_date" 
                                    :placeholder="@translate('Select Receipt Date')" 
                                    wire:model="receipt_date"
                                    max="{{ now()->toDateString() }}"  
                                />
                                <x-ui.alert error="receipt_date" />
                            </x-ui.col>

                            <!-- Business -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="business_id" name="Business" :required="true" />
                               <x-form.select id="business_id" wire:model.live="business_id">
                                    <option value="">--Select Business--</option>
                                    @foreach ($businesses as $business)
                                        @if (in_array($business->id, $assignBusiness))
                                            <option value="{{ $business->id }}">{{ $business->name }}</option>
                                        @endif
                                    @endforeach
                                </x-form.select>

                                <x-ui.alert error="business_id" />
                            </x-ui.col>


                           <!-- Business ID Value -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="business_id_value" name="Business ID Value" />
                                <x-form.select id="business_id_value" wire:model="business_id_value">
                                    <option value="">--Select Business ID--</option>
                                    @foreach ($businessValues as $value)
                                        <option value="{{ $value->id }}">{{ $value->value }}</option>
                                    @endforeach
                                </x-form.select>
                                <x-ui.alert error="business_id_value" />
                            </x-ui.col>



                            <!-- Amount Received -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="amount_received" name="Amount Received" :required="true" />
                                <x-form.input-text id="amount_received" type="number" :placeholder="@translate('Enter Amount')"
                                    wire:model.live="amount_received" 
                                    max="{{ $max_receivable }}" />
                                <x-ui.alert error="amount_received" />
                            </x-ui.col>

                            <!-- Receipt Image -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="receipt_image" name="Receipt Image" />
                                <x-form.input-file accept="image/*" wire:model="receipt_image" />
                                <x-ui.alert error="receipt_image" />
                            </x-ui.col>

                            <!-- Preview Image -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6 d-flex justify-content-center align-items-center" style="min-height: 120px;">
                                <!-- Loader while image is uploading -->
                                <div wire:loading wire:target="receipt_image" class="text-center">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <div class="mt-2 text-primary fw-semibold">Loading image...</div>
                                </div>

                                <!-- Show image after upload -->
                                <div wire:loading.remove wire:target="receipt_image">
                                    @if ($receipt_image)
                                        <img src="{{ $receipt_image->temporaryUrl() }}" width="100" height="100"
                                            style="object-fit: cover;">
                                    @endif
                                </div>
                            </x-ui.col>

                        </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>

                <div class="mb-4 text-end">
                    <a href="{{ route('drivers.index') }}" wire:navigate
                        class="btn btn-danger w-sm">@translate('Cancel')</a>
                    <button type="submit" class="btn btn-success w-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            @translate('Create')
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            @translate('creating...')
                        </span>
                    </button>
                </div>
            </form>
        </x-ui.col>
    </x-ui.row>
</div>
