<div>
    @if($showRejectModal)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Reject Request</h5>
                </div>

                <div class="modal-body">
                    <label class="form-label">Reason for rejection <span class="text-danger">*</span></label>
                    
                    <textarea class="form-control" 
                            wire:model.defer="reject_reason"
                            rows="4"
                            placeholder="Write reason here..."></textarea>

                    @error('reject_reason')
                        <span class="text-danger mt-2">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('showRejectModal', false)">Close</button>

                    <button class="btn btn-danger" wire:click="submitReject" wire:loading.attr="disabled">
                        <!-- Spinner shows only while submitReject is running -->
                        <span wire:loading wire:target="submitReject" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Submit Rejection
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

    <x-ui.message />
    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Request Recharge List" :href="route('request-recharge.create')" :add="$add_permission"/>
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
                    />
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>

    @if($showRejectModal)
    <script>
        document.body.style.overflow = 'hidden';
    </script>
    @else
    <script>
        document.body.style.overflow = 'auto';
    </script>
    @endif
</div>
