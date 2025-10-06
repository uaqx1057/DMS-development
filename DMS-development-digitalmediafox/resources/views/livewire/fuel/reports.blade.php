
<!-- Modal -->
<div wire:ignore.self class="modal fade" id="fuelReportModal" tabindex="-1" aria-labelledby="fuelReportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Generate Fuel Report</h5>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label>Start Date</label>
            <input type="date" wire:model="startDate" class="form-control">
             @error('startDate') <span class="text-danger">{{ $message }}</span> @enderror
          </div>
          <div class="col-md-6">
            <label>End Date</label>
            <input type="date" wire:model="endDate" class="form-control">
             @error('endDate') <span class="text-danger">{{ $message }}</span> @enderror
          </div>

          <div class="col-md-6">
            <label>Vehicle (Optional)</label>
            <select wire:model="vehicleId" class="form-select">
              <option value="">All Vehicles</option>
              @foreach($vehicles as $v)
                <option value="{{ $v->id }}">{{ $v->registration_number }}</option>
              @endforeach
            </select>
          </div>

         
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" wire:click="generate">Generate</button>
      </div>
    </div>
  </div>
</div>

    <div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Vehicle</th>
            <th>Total Fuel</th>
            <th>Total Cost</th>
            <th>Distance</th>
            <th>Refuels</th>
            <th>Avg Mileage</th>
            <th>Avg Cost/L</th>
        </tr>
        </thead>
        <tbody>
     @forelse($byVehicle as $v)
    <tr>
        <td><strong>{{ $v->vehicle->registration_number ?? 'Unknown' }}</strong></td>
        <td>{{ number_format($v->total_fuel, 2) }} L</td>
        <td>SAR {{ number_format($v->total_cost, 2) }}</td>
        <td>{{ number_format($v->total_distance, 2) }} km</td>
        <td>{{ $v->refuels }}</td>
        <td>{{ $v->avg_mileage }} km/L</td>
        <td>SAR {{ $v->avg_cost_per_liter }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted">No data available for the selected date range.</td>
    </tr>
@endforelse

        </tbody>
    </table>
    </div>

