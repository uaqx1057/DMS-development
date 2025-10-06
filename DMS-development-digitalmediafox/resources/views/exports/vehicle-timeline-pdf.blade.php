<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vehicle History Report</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
        .section-title { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Vehicle History Report</h2>

    <p><strong>Vehicle:</strong> {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->registration_number }})</p>
    <p><strong>VIN:</strong> {{ $vehicle->vin }} | <strong>Year:</strong> {{ $vehicle->year }} | <strong>Status:</strong> {{ $vehicle->status }}</p>
    <p><strong>Fuel Type:</strong> {{ $vehicle->fuel_type }} | <strong>Mileage:</strong> {{ $vehicle->mileage }} KM</p>
    <p><strong>Current Location:</strong> {{ $vehicle->current_location }}</p>
    <p><strong>Created At:</strong> {{ $vehicle->created_at->format('d-m-Y') }}</p>
    @if($vehicle->notes)
        <p><strong>Notes:</strong> {{ $vehicle->notes }}</p>
    @endif

    {{-- Maintenance Events --}}
    <div class="section-title">Maintenance History</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Urgency</th>
                <th>Status</th>
                <th>Requested By</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($maintenances as $m)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($m->created_at)->format('d-m-Y') }}</td>
                    <td>{{ \Illuminate\Support\Str::headline($m->maintenance_type) }}</td>
                    <td>{{ $m->description }}</td>
                    <td>{{ $m->urgency }}</td>
                    <td>{{ $m->status }}</td>
                    <td>{{ $m->request_by }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No maintenance records found.</td></tr>
            @endforelse
        </tbody>
    </table>

{{-- Replacement Vehicles --}}
<div class="section-title">Replacement History</div>
<table>
    <thead>
        <tr>
            <th>Maintenance Date</th>
            <th>Date</th>
            <th>Replacement Vehicle</th>
            <th>Period</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Notes</th>
           
        </tr>
    </thead>
    <tbody>
        @forelse ($replacements as $r)
            <tr>
                 <td>
                    @if($r->last_maintenance_date)
                        {{ \Carbon\Carbon::parse($r->last_maintenance_date)->format('d-m-Y') }}
                    @else
                        No Maintenance Record
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d-m-Y') }}</td>
                <td>
                    @if($r->replacementVehicle)
                        {{ $r->replacementVehicle->make }}
                        {{ $r->replacementVehicle->model }}
                        ({{ $r->replacementVehicle->registration_number }})
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ \Illuminate\Support\Str::headline($r->replacement_period) }}</td>
                <td>{{ \Illuminate\Support\Str::headline($r->reason) }}</td>
                <td>{{ $r->status }}</td>
                <td>{{ $r->notes }}</td>
               
            </tr>
        @empty
            <tr>
                <td colspan="7">No replacement records found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

    
    {{-- Driver Assignment History --}}
<div class="section-title">Driver Assignment History</div>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Driver</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($driverAssignments as $assignment)
            <tr>
                <td>{{ \Carbon\Carbon::parse($assignment->add_date)->format('d-m-Y') }}</td>
                <td>{{ e(optional($assignment->driver)->name ?? 'N/A') }}</td>
                <td>{{ e(ucfirst($assignment->status)) }}</td>
            </tr>
        @empty
            <tr><td colspan="3">No driver assignment records found.</td></tr>
        @endforelse
    </tbody>
</table>


</body>
</html>
