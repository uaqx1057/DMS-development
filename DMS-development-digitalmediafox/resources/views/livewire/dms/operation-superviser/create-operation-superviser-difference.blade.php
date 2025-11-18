<div>
    <x-layouts.breadcrumb :main_menu="$main_menu" :menu="$menu" />
    <x-ui.message />

    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <form wire:submit.prevent="save">
                <x-ui.card>
                    <x-ui.card-header title="Supervisor Difference" />
                    <x-ui.card-body>
                        <x-ui.row>

                            

                            <!-- Driver Receipts -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="superviser" name="Operation Superviser" :required="true" />
                               <x-form.select id="superviser" wire:model.live="superviser">
                                    <option value="">--select operation supervisor--</option>
                                    @foreach ($supervisers  as $superviser)
                                            <option value="{{ $superviser->id }}">{{ $superviser->name }}</option>
                                    @endforeach
                                </x-form.select>
                                <x-ui.alert error="superviser" />
                            </x-ui.col>

                            <!-- Total Receipt or Difference -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="total_receipt" name="Total Receipt" :required="false" />
                                <x-form.input-text class="bg-light" readonly id="total_receipt" type="number" :placeholder="@translate('')"
                                    wire:model="total_receipt"/>
                                <x-ui.alert error="total_receipt" />
                            </x-ui.col>

                            <!-- Total Paid -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="total_paid" name="Total Paid" :required="true" />
                                <x-form.input-text id="total_paid" type="number" max="{{ $total_receipt }}" :placeholder="@translate('Enter Amount')"
                                    wire:model.live="total_paid" />
                                <x-ui.alert error="total_paid" />
                            </x-ui.col>

                            <!-- Total Remaining -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="total_remaining" name="Total Remaining" :required="false" />
                                <x-form.input-text class="bg-light" readonly id="total_remaining" type="number" :placeholder="@translate('')"
                                    wire:model="total_remaining" />
                                <x-ui.alert error="total_remaining" />
                            </x-ui.col>

                            <!-- Receipt Image -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label name="Receipt Image" :required="true" />
                                <x-form.input-file wire:model="receipt_image" accept="image/*" />
                                <x-ui.alert error="receipt_image" />
                            </x-ui.col>

                            <!-- Image Preview -->
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
                            </x-ui.col>

                        </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>

                <div class="mb-4 text-end">
                    <a href="{{ route('superviser-difference.index') }}" wire:navigate
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
