<div>
    <x-layouts.breadcrumb :main_menu="$main_menu" :menu="$menu" />
    <x-ui.message />

    <x-ui.row>
        <x-ui.col class="col-lg-12">

            <form wire:submit.prevent="save">
                <x-ui.card>
                    <x-ui.card-header title="Create Rechage" />

                    <x-ui.card-body>
                        <x-ui.row>

                            <!-- Amount -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="amount" name="Amount" :required="true" />
                                <x-form.input-text id="amount" wire:model="amount" />
                                <x-ui.alert error="amount" />
                            </x-ui.col>

                            <!-- Date -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label for="date" name="Date" :required="true" />
                                <x-form.input-date-time id="date" wire:model="date" max="{{ now()->toDateString() }}" />
                                <x-ui.alert error="date" />
                            </x-ui.col>

                            <!-- Receipt Image -->
                            <x-ui.col class="mb-3 col-lg-6 col-md-6">
                                <x-form.label name="Image/PDF" :required="true"/>
                                <x-form.input-file wire:model="image" accept="image/*,application/pdf" />
                                <x-ui.alert error="image" />
                            </x-ui.col>

                            <!-- Image Preview -->
                            {{-- <x-ui.col class="mb-3 col-lg-6 col-md-6 d-flex justify-content-center align-items-center">
                                <div wire:loading wire:target="image">
                                    <div class="spinner-border text-primary"></div>
                                    <div class="mt-2 text-primary fw-semibold">Loading...</div>
                                </div>

                                <div wire:loading.remove wire:target="image">
                                    @if ($image)
                                        <img src="{{ $image->temporaryUrl() }}" width="100" height="100"
                                             style="object-fit: cover;">
                                    @endif
                                </div>
                            </x-ui.col> --}}

                        </x-ui.row>
                    </x-ui.card-body>
                </x-ui.card>

                <!-- Buttons -->
                <div class="mb-4 text-end">
                    <a href="{{ route('recharge.index') }}" wire:navigate class="btn btn-danger w-sm">Cancel</a>
                    <button type="submit" class="btn btn-success w-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Create</span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm"></span> creating...
                        </span>
                    </button>
                </div>

            </form>
        </x-ui.col>
    </x-ui.row>
</div>
