<?php
namespace App\Http\Controllers;

use App\Models\Fuel;
use Illuminate\Support\Facades\DB;

class FuelExportController extends Controller
{
    public function export()
    {
        $filename = 'fuel_requests_' . now()->format('d-m-Y') . '.csv';

        $columns = [
        'id'                    => 'ID',
        'fuel_type'             => 'Fuel Type',
        'vehicle_number'        => 'Vehicle',
        'requested_by_name'     => 'Requested By',
        'status'                => 'Status',
        'requested_amount_'     => 'Requested Amount',
        'reason'                => 'Reason for Request',
        'num_orders'            => 'Number of Orders',
        'additional_notes'      => 'Additional Notes',
        'notes'                 => 'Rejection Notes',
        'rejected_by_name'      => 'Rejected By',
        'approval_date'         => 'Approval Date',
        'approved_amount'       => 'Approved Amount',
        'approved_by'           => 'Approved By',
        'request_date'          => 'Request Date',
        'created'               => 'Created',
        'updated'               => 'Updated',
        ];


    $fuels = DB::table('fuels')
    ->leftJoin('fuel_request_rejects', function($join) {
        $join->on('fuels.id', '=', 'fuel_request_rejects.fuel_id')
             ->where('fuels.status', '=', 'rejected');
    })
    ->leftJoin('users as requester', 'fuels.requested_by', '=', 'requester.id')
    ->leftJoin('users as rejecter', 'fuel_request_rejects.rejected_by', '=', 'rejecter.id')
    ->leftJoin('vehicles', 'fuels.vehicle_id', '=', 'vehicles.id')
    ->leftJoin('fuel_request_approvals', 'fuels.id', '=', 'fuel_request_approvals.fuel_id')
    ->leftJoin('users as approver', 'fuel_request_approvals.approved_by', '=', 'approver.id')
    ->select(
        'fuels.id',
        'fuels.fuel_type',
        'vehicles.registration_number as vehicle_number',
        'requester.name as requested_by_name',
        'fuels.status',
        'fuels.request_amount as requested_amount_',
        'fuels.reason_for_request as reason',
        'fuels.number_of_order_deliver as num_orders',
        'fuels.additional_notes',

        // Rejection details
        'fuel_request_rejects.notes',
        'rejecter.name as rejected_by_name',

        // Approval details
        'fuel_request_approvals.created_at as approval_date',
        'fuel_request_approvals.approved_amount',
        'approver.name as approved_by',

        // Common timestamps
        'fuels.created_at as request_date',
        'fuels.created_at as created',
        'fuels.updated_at as updated'
    )
    ->orderBy('fuels.id', 'asc') // 👈 this line added
    ->get();



        $callback = function () use ($fuels, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array_values($columns)); // column headers

            foreach ($fuels as $row) {
                $csvRow = [];
                foreach (array_keys($columns) as $key) {
                    $csvRow[] = $row->{$key} ?? '';
                }
                fputcsv($file, $csvRow);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ]);
    }

    public function exportExpenses()
    {
    $filename = 'fuel_expenses_' . now()->format('d-m-Y') . '.csv';

    $columns = [
    'id'                          => 'ID',
    'vehicle_number'              => 'Vehicle',
    'fuel_type'                   => 'Fuel Type',
    'fuel_station'                => 'Fuel Station',
    'liters'                      => 'Liters',
    'amount_paid'                 => 'Amount Paid',
    'odometer_reading'           => 'Odometer',
    'distance_since_last_refuel' => 'Distance (km)',
    'receipt_image'              => 'Receipt Image',
    'notes'                       => 'Notes',
    'recorded_by_name'            => 'Recorded By',
    'created_at'                  => 'Record Created',
];

$expenses = DB::table('fuel_expenses')
    ->leftJoin('vehicles', 'fuel_expenses.vehicle_id', '=', 'vehicles.id')
    ->leftJoin('users as recorder', 'fuel_expenses.recorded_by', '=', 'recorder.id')
    ->select(
        'fuel_expenses.id',
        'vehicles.registration_number as vehicle_number',
        'fuel_expenses.fuel_type',
        'fuel_expenses.fuel_station',
        'fuel_expenses.liters',
        'fuel_expenses.amount_paid',
        'fuel_expenses.odometer_reading',
        'fuel_expenses.distance_since_last_refuel',
        'fuel_expenses.receipt_image',
        'fuel_expenses.notes',
        'recorder.name as recorded_by_name',
        'fuel_expenses.created_at',
    )
    ->orderBy('fuel_expenses.id', 'asc')
    ->get();


    $callback = function () use ($expenses, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, array_values($columns)); // headers

        foreach ($expenses as $row) {
            $csvRow = [];
            foreach (array_keys($columns) as $key) {
                $csvRow[] = $row->{$key} ?? '';
            }
            fputcsv($file, $csvRow);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=\"$filename\"",
    ]);
}



}


?>