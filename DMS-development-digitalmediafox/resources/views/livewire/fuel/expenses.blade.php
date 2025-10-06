<div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                  <th>Vehicle</th>
                  <th>Date</th>
                  <th>Fuel Type</th>
                  <th>Liters</th>
                  <th>Amount Paid</th>
                  <th>Station</th>
                  <th>Distance</th>
                  <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fuelExpenses as $expense)
                    <tr>
                        <td>{{ $expense->vehicle->registration_number ?? 'N/A' }}</td>
                        <td>{{ $expense->created_at->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($expense->fuel_type) }}</td>
                        <td>{{ $expense->liters }}</td>
                        <td>{{ $expense->amount_paid }}</td>
                        <td>{{ $expense->fuel_station }}</td>
                        <td>{{ $expense->distance_since_last_refuel }} KM</td>
                        <td>

                          <button class="btn btn-sm btn-outline-info" wire:click="showExpenseDetails({{ $expense->id }})">Details</button>

                            @if($expense->receipt_image)
                                <a class="btn btn-sm btn-outline-success" href="{{ asset($expense->receipt_image) }}" target="_blank">View Receipt</a>
                            @else
                                -
                            @endif
                        </td>
                        
                        
                    </tr>
                @empty
                    <tr><td colspan="10">No fuel expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $fuelExpenses->links() }} {{-- If using pagination --}}
    </div>


<div wire:ignore.self class="modal fade" id="fuelExpenseModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <form wire:submit.prevent="saveFuelExpense" enctype="multipart/form-data">
      <div class="modal-content bg-light">
        <div class="modal-header">
          <h5 class="modal-title">Record Fuel Expense</h5>
        </div>

        <div class="modal-body">
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Vehicle</label>
              <select wire:model="vehicle_id" class="form-select">
                <option value="">Select vehicle...</option>
                @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_number }} ({{ $vehicle->make }} {{ $vehicle->model }})</option>
                @endforeach
              </select>
              @error('vehicle_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Fuel Type</label>
              <select wire:model="fuel_type" class="form-select">
                <option value="">Select Fuel Type</option>
                <option value="petrol">Petrol</option>
                <option value="diesel">Diesel</option>
                <option value="cng">CNG</option>
                <option value="electric">Electric</option>
              </select>
              @error('fuel_type') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Fuel Station</label>
              <input type="text" wire:model="fuel_station" class="form-control" placeholder="Enter station name">
              @error('fuel_station') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Liters</label>
              <input type="number" step="0.01" wire:model="liters" class="form-control" placeholder="e.g. 25.5">
              @error('liters') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Amount Paid</label>
              <input type="number" step="0.01" wire:model="amount_paid" class="form-control" placeholder="e.g. 1500">
              @error('amount_paid') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Current Odometer Reading</label>
              <input type="number" wire:model="odometer_reading" class="form-control" placeholder="e.g. 47520">
              @error('odometer_reading') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Distance Since Last Refuel (km)</label>
              <input type="number" wire:model="distance_since_last_refuel" class="form-control" placeholder="e.g. 300">
              @error('distance_since_last_refuel') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Upload Receipt Image</label>
              <input type="file" wire:model="receipt_image" class="form-control">
              @error('receipt_image') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-12">
              <label class="form-label">Notes</label>
              <textarea wire:model="notes" class="form-control" rows="2" placeholder="Any additional comments..."></textarea>
              @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

          </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save Expense</button>
           <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="expenseDetailsModal" tabindex="-1" aria-labelledby="expenseDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fuel Expense Details</h5>
            </div>

            <div class="modal-body">
                @if($selectedExpense)
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Vehicle:</strong> {{ $selectedExpense->vehicle->registration_number ?? '-' }}</li>
                        <li class="list-group-item"><strong>Fuel Type:</strong> {{ ucfirst($selectedExpense->fuel_type) }}</li>
                        <li class="list-group-item"><strong>Fuel Station:</strong> {{ $selectedExpense->fuel_station }}</li>
                        <li class="list-group-item"><strong>Liters:</strong> {{ $selectedExpense->liters }}</li>
                        <li class="list-group-item"><strong>Amount Paid:</strong> {{ number_format($selectedExpense->amount_paid, 2) }}</li>
                        <li class="list-group-item"><strong>Odometer Reading:</strong> {{ $selectedExpense->odometer_reading }} km</li>
                        <li class="list-group-item"><strong>Distance Since Last Refuel:</strong> {{ $selectedExpense->distance_since_last_refuel }} km</li>
                        <li class="list-group-item"><strong>Recorded By:</strong> {{ $selectedExpense->recordedBy->name ?? 'N/A' }}</li>
                        <li class="list-group-item"><strong>Notes:</strong> {{ $selectedExpense->notes ?? '-' }}</li>

                        
                    </ul>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

