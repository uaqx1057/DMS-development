<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateFuelRequest;
use App\Models\FuelRequest;
use Illuminate\Http\Request;

class FuelRequestController extends Controller
{

    public function index(Request $request)
    {
        try {
            // Fetch the authenticated driver
            $driver = $request->user();

            // Get the filter for status from the request (optional)
            $status = $request->query('status'); // e.g., 'Pickup', 'Drop', 'Cancel'

            // Start the query to get the orders for the driver
            $query = $driver->fuel_requests();

            // Apply the status filter if provided
            if ($status) {
                $query->where('status', $status);
            }

            // Get the filtered orders
            $orders = $query->get();

            return sendResponse(200, 'success', 'Fuel Requests fetched successfully', $orders);
        } catch (\Exception $e) {
            return sendResponse(500, 'error', 'Something went wrong', $e->getMessage());
        }
    }

    /**
     * Handle multi-file upload and save total orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateFuelRequest $request)
    {
        try {

            $driver = $request->user();
            // Initialize an empty array to store file paths
            $filePaths = [];

            // Loop through each file and store it
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    // Check if the file is valid before storing it
                    if ($file->isValid()) {
                        $path = $file->store('fuel_requests', 'public');
                        $filePaths[] = $path; // Save file path to the array
                    } else {
                        return response()->json(['error' => 'One or more files failed to upload.'], 400);
                    }
                }
            }

            // Create a new Fuel record
            $fuel = $driver->fuel_requests()->create([
                'total_orders' => $request->input('total_orders'),
                'files' => $filePaths,
            ]);

            return sendResponse(201, 'success', 'Fuel Request sent successfully!', $fuel);

        } catch (\Exception $e) {
            return sendResponse(500, 'error', 'something went wrong', $e->getMessage());
        }
    }
}
