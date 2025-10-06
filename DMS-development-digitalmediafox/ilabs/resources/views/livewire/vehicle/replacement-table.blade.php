@if ($replacementRequests->isEmpty())
    <p class="text-muted">No maintenance requests found.</p>
@else
    <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Vehicle</th>
            <th>Reason</th>
            <th>Period</th>
            <th>Status</th>
            <th>Notes</th>
            <th>Requested At</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($replacementRequests as $request)
            <tr>
              <td>{{ $request->vehicle->make }} / {{ $request->vehicle->model }} ({{ $request->vehicle->year }})</td>
              <td>{{ ucfirst(str_replace('_', ' ', $request->reason)) }}</td>
              <td>{{ ucfirst($request->period) }}</td>
              <td>
                <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'pending' ? 'warning' : 'secondary') }}">
                  {{ ucfirst($request->status) }}
                </span>
              </td>
              <td>{{ $request->notes }}</td>
              <td>{{ $request->created_at->format('d M Y') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">No replacement requests found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
       {{ $replacementRequests->links() }}
    </div>
@endif
