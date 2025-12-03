<div>

    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

    <x-ui.message />
    <x-ui.row>
        <div class="row">
            <!-- Begin: Date Range --> 
            <x-ui.col class="mb-3 col-lg-3 col-md-3">
                <x-form.label for="daterangepicker" name="Date Range"/>
                <input type="text" id="daterangepicker" class="form-control" placeholder="Select Date Range" >
            </x-ui.col>
            <!-- End: Date Range --> 

            <!-- Begin: Branch Card --> 
            <x-ui.col class="mb-3 col-lg-3 col-md-3">
                <x-form.label for="status" name="Status"/>
                <x-form.select class="form-select" wire:model.live="status">
                    <x-form.option value="" name="--select status--" />
                    <x-form.option value="Pending" name="Pending"/>
                    <x-form.option value="Accepted" name="Accepted"/>
                    <x-form.option value="Rejected" name="Rejected"/>
                    <x-form.option value="Recharged" name="Recharged"/>
                </x-form.select>
                <x-ui.alert error="status"/>
            </x-ui.col>
            <!-- End: Branch Card -->
        </div>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Recharge Logs" :href="route('request-recharge.create')" :add="$add_permission" :export="$requestRecharges->count() > 0" />
                <x-ui.card-body>
                    <x-table
                        :columns="$columns"
                        :page="$page"
                        :perPage="$perPage"
                        :items="$requestRecharges"
                        :sortColumn="$sortColumn"
                        :sortDirection="$sortDirection"
                        isModalEdit="true"
                        routeEdit="booklet.edit"
                        :edit_permission="$edit_permission"
                        :rechargePermission="true"
                        :rechargeLog="true"
                    />
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('livewire:init', () => {

            flatpickr("#daterangepicker", {
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const start = instance.formatDate(selectedDates[0], "Y-m-d");
                        const end   = instance.formatDate(selectedDates[1], "Y-m-d");
                        
                        Livewire.dispatch("dateRangeSelected", { start, end });
                    } else if (selectedDates.length === 0) {
                        // Clear the date range when input is emptied
                        Livewire.dispatch("dateRangeSelected", { start: null, end: null });
                    }
                }
            });

        });
    </script>
    @endpush
</div>
