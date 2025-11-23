<div>
    <x-layouts.breadcrumb :main_menu="$main_menu" :menu="$menu" />
    <x-ui.message />

    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit.prevent="save">
                <x-ui.card>
                    <x-ui.card-header title="Add Amount Transfer" />
                    <x-ui.card-body>
                        <x-ui.row>

                            
                            @if(auth()->user()->role_id != 8 )
                            <!-- Operation supervisor -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="supervisor" name="Operation Supervisor" :required="true" />
                               <x-form.select id="supervisor" wire:model="supervisor">
                                    <option value="">--select operation supervisor--</option>
                                    @foreach ($supervisors  as $supervisor)
                                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                    @endforeach
                                </x-form.select>
                                <x-ui.alert error="supervisor" />
                            </x-ui.col>
                            @endif
                            <!-- Type -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="payment_type" name="Type" :required="true" />
                               <x-form.select id="payment_type" wire:model="payment_type">
                                    <option value="">--select payment type--</option>
                                        <option value="bank transfer">bank transfer</option>
                                        <option value="cash">cash</option>
                                </x-form.select>
                                <x-ui.alert error="payment_type" />
                            </x-ui.col>

                            <!-- Amount -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="amount" name="Amount" :required="true" />
                                <x-form.input-text id="amount" type="number" :placeholder="@translate('Enter Amount')"
                                    wire:model="amount" />
                                <x-ui.alert error="amount" />
                            </x-ui.col>

                            <!-- Date -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label for="receipt_date" name="Date" :required="true" />
                                <x-form.input-date id="receipt_date" wire:model="receipt_date" max="{{ now()->toDateString() }}" />
                                <x-ui.alert error="receipt_date" />
                            </x-ui.col>

                            <!-- Receipt Image -->
                            <x-ui.col class="mb-3 col-lg-4 col-md-6">
                                <x-form.label name="Receipt Image/PDF" :required="true" />
                                <x-form.input-file wire:model="receipt_image" accept="image/*,application/pdf" />
                                <x-ui.alert error="receipt_image" />
                            </x-ui.col>

                            {{-- <!-- Image Preview -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6 d-flex justify-content-center align-items-center">
                                <div wire:loading wire:target="receipt_image">
                                    <div class="spinner-border text-primary"></div>
                                    <div class="mt-2 text-primary fw-semibold">Loading...</div>
                                </div>

                                <div wire:loading.remove wire:target="receipt_image">
                                    @if ($receipt_image)
                                        <img src="{{ $receipt_image->temporaryUrl() }}" width="100" height="100"
                                             style="object-fit: cover;">
                                    @endif
                                </div>
                            </x-ui.col> --}}

                        </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>

                <div class="mb-4 text-end">
                    <a href="{{ route('amount-transfer.index') }}" wire:navigate
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
