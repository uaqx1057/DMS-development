<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />
    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit='create'>
                <!-- Begin: Business Details -->
                <x-ui.card>
                    <x-ui.card-header title="Business Details" />
                    <x-ui.card-body>
                    <x-ui.row>
                        <!-- Begin: Driver Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="driver_id" name="Drivers" :required='true'/>
                            <x-form.select class="form-select" wire:model.live="driver_id" wire:change="onDriverChange">
                                <x-form.option value="" name="--select driver--"/>
                                @foreach ($drivers as $item)
                                    @php $name = $item->name . ' ( ' . $item->iqaama_number . ' )'; @endphp
                                    <x-form.option value="{{ $item->id }}" :name="$name"/>
                                @endforeach
                            </x-form.select>
                            <x-ui.alert error="driver_id"/>
                        </x-ui.col>
                        <!-- End: Driver Card -->

                        <!-- Begin: Date of Birth Card -->
                        <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="report_date" name="Report Date" :required="true"/>
                            <x-form.input-date  name="report_date" :placeholder="@translate('Report Date')"  wire:model='report_date'/>
                            <x-ui.alert error="report_date"/>
                        </x-ui.col>
                        <!-- End: Date of Birth Card -->
                    </x-ui.row>
                    
                    <!-- Business Selection (Show when driver is selected) -->
                    @if($driver_id)
                    <x-ui.row>
                        <x-ui.col class="mb-3 col-lg-12 col-md-12">
                            <x-form.label for="" name="Select Business Types"/>
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
                                    wire:model.live="business_ids"
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
                    @else
                    <x-ui.row>
                        <x-ui.col class="col-lg-12">
                            <div class="alert alert-info">
                                Please select a driver first to choose businesses
                            </div>
                        </x-ui.col>
                    </x-ui.row>
                    @endif

                    <!-- Business IDs Selection (Show when businesses are selected) -->
                    @if(!empty($availableBusinessIds) && $driver_id && count($business_ids) > 0)
                        <x-ui.row>
                            <x-ui.col class="mb-1 col-lg-12 col-md-12">
                                <x-form.label for="" name="Select Business IDs"/>
                            </x-ui.col>
                        </x-ui.row>
                        
                        @foreach($availableBusinessIds as $businessTypeId => $businessIds)
                            @if($businessIds->count() > 0 && in_array($businessTypeId, $business_ids))
                                <x-ui.row class="mb-2">
                                    <x-ui.col class="mb-2 col-lg-12">
                                        <h6 class="text-muted">{{ $businesses->firstWhere('id', $businessTypeId)->name }} - All Available IDs</h6>
                                        <div class="row">
                                            @foreach($businessIds as $id)
                                                <div class="col-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            id="business_id_{{ $id->id }}"
                                                            value="{{ $id->id }}"
                                                            wire:model.live="selectedBusinessIds"
                                                        />
                                                        <label class="form-check-label" for="business_id_{{ $id->id }}">
                                                            {{ $id->value }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </x-ui.col>
                                </x-ui.row>
                            @elseif(in_array($businessTypeId, $business_ids))
                                <x-ui.row class="mb-2">
                                    <x-ui.col class="col-lg-12">
                                        <div class="alert alert-warning">
                                            No Business IDs available for {{ $businesses->firstWhere('id', $businessTypeId)->name }}
                                        </div>
                                    </x-ui.col>
                                </x-ui.row>
                            @endif
                        @endforeach
                        <x-ui.alert error="selectedBusinessIds"/>
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
                    <button type="submit" class="btn btn-success w-sm" @if(empty($driver_id) || empty($selectedBusinessIds)) disabled @endif>@translate('Create')</button>
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