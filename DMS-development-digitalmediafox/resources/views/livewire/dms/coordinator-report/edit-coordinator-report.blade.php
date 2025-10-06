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
                            <x-form.select class="form-select" wire:model='driver_id'>
                                <x-form.option value="" name="--select driver--"/>
                                @foreach ($drivers as $item)
                                    <x-form.option value="{{ $item->id }}" :name="$item->name"/>
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

                         <!-- Begin: Status Card -->
                         <x-ui.col class="mb-3 col-lg-3 col-md-3">
                            <x-form.label for="status" name="Status"/>
                            <x-form.select class="form-select" wire:model='status'>
                                    <x-form.option value="Pending" name="Pending"/>
                                    <x-form.option value="Approved" name="Approved"/>
                            </x-form.select>
                            <x-ui.alert error="status"/>
                        </x-ui.col>
                        <!-- End: Driver Card -->

                    </x-ui.row>
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
                                        wire:change="handleBusinessSelectionUpdated"
                                    />
                                    <label class="form-check-label" for="business_{{$business['id']}}">
                                        {{ $business['name'] }}
                                    </label>
                                </div>
                            @endforeach
                            <x-ui.alert error="business_ids"/>
                        <!-- End: Business Card -->
                    </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>
                <!-- End: Business Details -->

                @foreach ($businesses_with_fields as $business)
                <!-- Begin: Details Card for Each Business -->
                <x-ui.card>
                    <x-ui.card-header title="{{ $business->name }}"/>
                    <x-ui.card-body>
                        <x-ui.row>

                            @foreach ($business->fields as $item)
                                <!-- Begin: Dynamic Input Field for Each Business -->
                                <x-ui.col class="mb-3 col-lg-3 col-md-3">
                                    <x-form.label for="{{ $item->short_name }}" name="{{ $item->name }}" :required="$item->required"/>

                                    @if ($item->type === 'TEXT')
                                        <x-form.input-text id="{{ $item->short_name }}"
                                            wire:model.defer="formData.{{ $business->id }}.{{ $item->short_name }}"
                                            :placeholder="@translate($item->name)"/>
                                    @elseif ($item->type === 'INTEGER')
                                        <x-form.input-number id="{{ $item->short_name }}"
                                            wire:model.defer="formData.{{ $business->id }}.{{ $item->short_name }}"
                                            :placeholder="@translate($item->name)"/>
                                    @elseif ($item->type === 'DOCUMENT')
                                        <x-form.input-multifile id="{{ $item->short_name }}"
                                            wire:model="files.{{ $business->id }}.{{ $item->short_name }}"
                                            multiple
                                            :placeholder="@translate($item->name)"/>

                                        <!-- Display Uploaded Documents in a Row of Col-4 -->
                                        @php
                                            $documents = isset($formData[$business->id][$item->short_name]) ? json_decode($formData[$business->id][$item->short_name], true) : [];
                                        @endphp

                                        @if (is_array($documents) && count($documents) > 0)
                                            <div class="row">
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

                                    <x-ui.alert error="formData.{{ $business->id }}.{{ $item->short_name }}"/>
                                </x-ui.col>
                                <!-- End: Dynamic Input Field for Each Business -->
                            @endforeach

                        </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>
                <!-- End: Details Card for Each Business -->
            @endforeach


                <!-- end card -->
                <div class="mb-4 text-end">
                    <a href="{{ route('coordinator-report.index') }}" wire:navigate class="btn btn-danger w-sm">@translate('Cancel')</a>
                    @if ($business->status === 'Approved')
                        <button type="submit" class="btn btn-success w-sm">@translate('Can\'t Update Already Approved Report')</button>
                    @else
                        <button type="submit" class="btn btn-success w-sm">@translate('Update')</button>
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
