<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />
    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit='update'>
                <!-- Begin: Business Details -->
                <x-ui.card>
                    <x-ui.card-header title="Business Details"/>
                    <x-ui.card-body>
                    <x-ui.row>
                        <!-- Begin: Driver Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="driver_id" name="Drivers" :required='true'/>
                            <x-form.select class="form-select" wire:model.live="driver_id" wire:change="onDriverChange">
                                <x-form.option value="" name="--select driver--"/>
                                @foreach ($drivers as $item)
                                    <x-form.option value="{{ $item->id }}" :name="$item->name"/>
                                @endforeach
                            </x-form.select>
                            <x-ui.alert error="driver_id"/>
                        </x-ui.col>
                        <!-- End: Driver Card -->

                        <!-- Begin: Report Date Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="report_date" name="Report Date" :required="true"/>
                            <x-form.input-date  name="report_date" :placeholder="@translate('Report Date')"  wire:model='report_date' max="{{ now()->format('Y-m-d') }}"/>
                            <x-ui.alert error="report_date"/>
                        </x-ui.col>
                        <!-- End: Report Date Card -->

                        <!-- Begin: Status Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="status" name="Status"/>
                            <x-form.select class="form-select" wire:model='status'>
                                <x-form.option value="Pending" name="Pending"/>
                                <x-form.option value="Approved" name="Approved"/>
                            </x-form.select>
                            <x-ui.alert error="status"/>
                        </x-ui.col>
                        <!-- End: Status Card -->
                    </x-ui.row>
                    
                    <!-- Business Selection (Show when driver is selected) -->
                    @if($driver_id)
                        @if($businesses->count() > 0)
                            <x-ui.row>
                                <x-ui.col class="mb-3 col-lg-12 col-md-12">
                                    <x-form.label for="" name="Select Business Types & IDs"/>
                                </x-ui.col>
                            </x-ui.row>
                            
                            <!-- Display businesses in columns with IDs below each -->
                            <x-ui.row class="px-3">
                                @foreach ($businesses as $business)
                                    <x-ui.col class="mb-4 col-lg-4 col-md-6">
                                        <div class="border rounded p-3 h-100">
                                            <!-- Business Checkbox -->
                                            <div class="form-check form-check-secondary mb-3">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    id="business_{{$business['id']}}"
                                                    value="{{ $business['id'] }}"
                                                    wire:model.live="business_ids"
                                                    wire:change="handleBusinessSelection"
                                                />
                                                <label class="form-check-label fw-bold" for="business_{{$business['id']}}">
                                                    {{ $business['name'] }}
                                                </label>
                                            </div>

               <!-- Business IDs (Show when business is selected) -->
@if(in_array($business['id'], $business_ids))
    @if(isset($availableBusinessIds[$business['id']]) && $availableBusinessIds[$business['id']]->count() > 0)
        <div class="mt-2 ps-3">
            <small class="text-muted d-block mb-2">Available IDs:</small>
            @foreach($availableBusinessIds[$business['id']] as $id)
                <div class="form-check mb-2">
                    <input class="form-check-input"
                        type="checkbox"
                        id="business_id_{{ $id->id }}"
                        value="{{ $id->id }}"
                        {{ in_array($id->id, $selectedBusinessIds) ? 'checked' : '' }}
                        wire:change="toggleBusinessId({{ $id->id }}, $event.target.checked)"
                    />
                    <label class="form-check-label" for="business_id_{{ $id->id }}">
                        {{ $id->value }}
                    </label>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning py-2 px-3 mb-0">
            <small>No IDs available</small>
        </div>
    @endif
@endif
                                        </div>
                                    </x-ui.col>
                                @endforeach
                            </x-ui.row>
                            <x-ui.row>
                                <x-ui.col class="col-lg-12">
                                    <x-ui.alert error="business_ids"/>
                                    <x-ui.alert error="selectedBusinessIds"/>
                                </x-ui.col>
                            </x-ui.row>
                        @else
                            <x-ui.row>
                                <x-ui.col class="col-lg-12">
                                    <div class="alert alert-warning">
                                        No businesses with available IDs found for the selected driver
                                    </div>
                                </x-ui.col>
                            </x-ui.row>
                        @endif
                    @else
                        <x-ui.row>
                            <x-ui.col class="col-lg-12">
                                <div class="alert alert-info">
                                    Please select a driver first to choose businesses
                                </div>
                            </x-ui.col>
                        </x-ui.row>
                    @endif

                    </x-ui.card-body>
                </x-ui.card>
                <!-- End: Business Details -->

                <!-- Show Fields for Each Selected Business ID -->
                @if(count($selectedBusinessIds) > 0)
                    @foreach($selectedBusinessIds as $businessIdValue)
                        @php
                            $businessId = \App\Models\BusinessId::find($businessIdValue);
                        @endphp
                        @if($businessId && isset($businesses_with_fields[$businessId->business_id]))
                            @php
                                $business = $businesses_with_fields[$businessId->business_id];
                                $businessType = $businesses->firstWhere('id', $businessId->business_id);
                            @endphp
                            <!-- Begin: Details Card for Each Business ID -->
                            <x-ui.card>
                                <x-ui.card-header title="{{ $businessType->name }} - ID: {{ $businessId->value }}"/>
                                <x-ui.card-body>
                                <x-ui.row>

                                @foreach ($business->fields as $item)
                                    <!-- Begin: Dynamic Input Field for Each Business ID -->
                                    <x-ui.col class="mb-3 col-lg-3 col-md-3">
                                        <x-form.label for="{{ $item->short_name }}_{{$businessIdValue}}" name="{{ $item->name }}" :required="$item->required"/>

                                        @if ($item->type === 'TEXT')
                                            <x-form.input-text id="{{ $item->short_name }}_{{$businessIdValue}}"
                                                wire:model="formData.{{ $businessIdValue }}.{{ $item->short_name }}"
                                                :placeholder="@translate($item->name)"/>
                                        @elseif ($item->type === 'INTEGER')
                                            <x-form.input-number id="{{ $item->short_name }}_{{$businessIdValue}}"
                                                wire:model="formData.{{ $businessIdValue }}.{{ $item->short_name }}"
                                                :placeholder="@translate($item->name)"/>
                                        @elseif ($item->type === 'DOCUMENT')
                                            <x-form.input-multifile id="{{ $item->short_name }}_{{$businessIdValue}}"
                                                wire:model="files.{{ $businessIdValue }}.{{ $item->short_name }}"
                                                multiple
                                                :placeholder="@translate($item->name)"/>

                                            <!-- Display Uploaded Documents -->
                                            @php
                                                $documents = isset($formData[$businessIdValue][$item->short_name]) ? json_decode($formData[$businessIdValue][$item->short_name], true) : [];
                                            @endphp

                                            @if (is_array($documents) && count($documents) > 0)
                                                <div class="row mt-2">
                                                    @foreach ($documents as $document)
                                                        <div class="mb-3 col-4">
                                                            <a href="{{ asset('storage/'. $document) }}" target="_blank">
                                                                <img src="{{ asset('storage/'. $document) }}" alt="Uploaded Document" class="img-fluid" style="width: 200px; height: 200px; object-fit: cover;" />
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif

                                        <x-ui.alert error="formData.{{ $businessIdValue }}.{{ $item->short_name }}"/>
                                    </x-ui.col>
                                    <!-- End: Dynamic Input Field for Each Business ID -->
                                @endforeach

                                </x-ui.row>
                                </x-ui.card-body>
                            </x-ui.card>
                            <!-- End: Details Card for Each Business ID -->
                        @endif
                    @endforeach
                @endif

                <!-- end card -->
                <div class="mb-4 text-end">
                    <a href="{{ route('coordinator-report.index') }}" wire:navigate class="btn btn-danger w-sm">@translate('Cancel')</a>
                    @if ($status === 'Approved')
                        <button type="button" class="btn btn-success w-sm" disabled>@translate('Can\'t Update Already Approved Report')</button>
                    @else
                        <button type="submit" class="btn btn-success w-sm" @if(empty($driver_id) || empty($selectedBusinessIds)) disabled @endif>@translate('Update')</button>
                    @endif
                </div>
            </form>
        </x-ui.col>
    </x-ui.row>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
@endpush