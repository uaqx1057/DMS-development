<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DriverStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginApiRequest;
use App\Http\Requests\Api\V1\CheckinRequest;
use App\Http\Requests\Api\V1\CheckoutRequest;
use App\Models\Field;
use App\Models\Driver;
use App\Models\DriverAttendance;
use App\Models\DriverDevice;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(LoginApiRequest $request) {
        try {
            $driver = Driver::where([
                'iqaama_number' => $request->iqaama_number,
                'dob' => $request->dob,
            ])->with('devices')->first();

            if (!$driver) {
                return sendResponse(404, 'error', 'Driver Not Found');
            }

            if ($driver->status === DriverStatus::Blocked->value) {
                return sendResponse(404, 'error', 'Your account has been temporarily blocked. Please contact support');
            }

            // Update driver's status
            // $driver->status = DriverStatus::Inactive->value;
            $driver->save();

            // Check if this device already exists for the driver
            $device = DriverDevice::where('driver_id', $driver->id)
                ->where('device_id', $request->device_id)
                ->first();

            if ($device) {
                // Update the existing device's FCM token
                $device->fcm_token = $request->fcm_token;
                $device->save();
            } else {
                // Add a new device for the driver
                $driver->devices()->create([
                    'fcm_token' => $request->fcm_token,
                    'device_id' => $request->device_id,
                ]);
            }

            // Generate a new token
            $driver->token = $driver->createToken('driver-token')->plainTextToken;

            // Return driver with devices
            return sendResponse(200, 'success', 'Logged In Successfully', [
                'driver' => $driver->fresh(['devices']),
                'token' => $driver->token,
            ]);

        } catch (\Exception $e) {
            return sendResponse(500, 'error', 'Something went wrong', $e->getMessage());
        }
    }

    public function checkin(CheckinRequest $request) {
        try{
            // Check If Driver Status Is InActive
            $driver = Driver::with('orders')->find($request->user()->id);

            if($driver->status === DriverStatus::Inactive->value){

            // Previous Checkin_time Date and Current Date Shoulde not be same
            $sameDayCheckinTimeCheck = DriverAttendance::where('driver_id', $driver->id)
                ->whereDate('checkin_time', Carbon::now())
                ->orderBy('id', 'desc')
                ->first();

            if($sameDayCheckinTimeCheck){
                return sendResponse(400, 'error', 'You can not checkin twice in same date');
            }

            // Get Pending Orders Count
            $pendingOrdersCount = $driver->orders()->where('status', OrderStatus::Pickup->value)->count();
            // Get Previous Attendance
            $previousAttendance = DriverAttendance::where('driver_id', $driver->id)->orderBy('id', 'desc')->first();
            $previousCheckout = $previousAttendance ? $previousAttendance->checkout_time : '';

            if($pendingOrdersCount > 0 || $previousCheckout !== ''){
                $driver->status = DriverStatus::Active->value;
                $driver->update();
                return sendResponse(200, 'success', 'Checkin Successfull', $driver);
            }




                // CheckIn Logic
                $checkInData = [
                    'checkin_time' => now(),
                    'meter_reading' => $request->meter_reading,
                    'meter_image' =>  $request->file('meter_image')->store('MeterImages', 'public'),
                    'car_image' => $request->hasFile('car_image') ? $request->file('meter_image')->store('CarImages', 'public') : ''
                ];

                $driver->driver_attendance()->create($checkInData);
                // Updating Driver Status
                $driver->status = DriverStatus::Active->value;

                $driver->save();
                $driver = $driver->fresh();

                return sendResponse(200, 'success', 'Checkin Successfull', $driver);
            }else{
                return sendResponse(400, 'error', 'Already CheckedIn');
            }

            // Check If Driver Previously Checkin
        }catch(\Exception $e){
            return sendResponse(500, 'error', 'something went wrong', $e->getMessage());
        }
    }

    public function checkout(CheckoutRequest $request) {
        try{
            // Check If Driver Status Is InActive
            $driver = $request->user();

            // * Check If User Already CheckedIn Today
            $latestCheckIn = getLastCheckIn();
            if(!$latestCheckIn){
                return sendResponse(400, 'error', 'No check in found.');
            }

            if($driver->status === DriverStatus::Inactive->value){
                return sendResponse(400, 'error', 'Already Check Out');
            }

            // * Check If User Has Pending Orders
            $pendingOrders = $latestCheckIn ? $driver->orders()->where([
                'status' => OrderStatus::Pickup->value,
                'driver_attendance_id' => $latestCheckIn->id,
            ])->count() : 0;

            if($pendingOrders > 0){
                 return sendResponse(400, 'error', 'Have you delivered your Order');
            }

              // Define validation rules
            $rules = [
                'files' => 'sometimes|array',
                'files.*' => 'file|mimes:jpg,jpeg,png|max:2048', // Each file must be a valid image
                'business_ids' => 'sometimes|array', // Ensure business_ids is provided as an array
                'business_ids.*' => 'exists:businesses,id', // Validate that each business_id exists
            ];

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // Check for validation errors
            if ($validator->fails()) {
                // Throw an exception with custom JSON response on validation failure
                throw new HttpResponseException(response()->json([
                    'code' => 422,
                    'status' => 'error',
                    'message' => 'Validation errors',
                    'response' => $validator->errors()
                ], 422));
            }

            // Get Last CheckIn Orders
            $latestDroppedOrders = getLatestDroppedOrders();

            // * Coordinator Report
            $coordinatorReport = $driver->coordinator_report()->create([
                'report_date' => Carbon::today()->toDateString(),
                'wallet' => $request->hasFile('wallet') ? $request->file('wallet')->store('wallet_images', 'public') : null
            ]);

            if($request->has('business_ids')){
                $coordinatorReport->businesses()->attach($request->business_ids);
            }
            // * Create Coordinator Report By Business
            foreach($latestDroppedOrders as $order){
                $business_id = $order->business_id;

                // * Get Business Fields
                $businessFieldTotalOrders = Field::where([
                    'short_name' => 'total_orders'
                ])->first();
                 // * Get Business Fields
                 $businessFieldDocuments = Field::where([
                    'short_name' => 'upload_driver_documents'
                ])->first();

                // * Adding Coordinator Report Field Values For Total Orders
                $coordinatorReport->report_fields()->create([
                    'field_id' => $businessFieldTotalOrders->id,
                    'value' => $order->total_dropped_orders,
                    'business_id' => $business_id
                ]);

                // Initialize an array to store file paths
                $filePaths = [];

                // Check if files are provided in the request
                if ($request->hasFile('files')) {
                    // Ensure 'files' is sent as an array, with each file having an associated business_id
                    $files = $request->file('files');
                    $businessIds = $request->input('business_ids'); // Assume that 'business_ids' input is provided for each file

                    // Validate that the number of files and business IDs match
                    if (count($files) !== count($businessIds)) {
                        return response()->json(['error' => 'Number of files and business_ids must match.'], 400);
                    }

                    // Loop through each file and its corresponding business_id
                    foreach ($files as $index => $file) {
                        if ($file->isValid()) {
                            $path = $file->store('coordinator_reports', 'public'); // Store the file in 'public/fuel_requests'

                            // Save file paths grouped by their business_id
                            $filePaths[$businessIds[$index]][] = $path;
                        } else {
                            return response()->json(['error' => 'One or more files failed to upload.'], 400);
                        }
                    }

                    // Save file paths to the coordinator report field
                    if (isset($filePaths[$business_id])) {
                        // Adding Coordinator Report Field Values For Uploaded Files
                        $coordinatorReport->report_fields()->create([
                            'field_id' => $businessFieldDocuments->id,
                            'value' => json_encode($filePaths[$business_id]), // Save the array of file paths as a JSON-encoded string
                            'business_id' => $business_id
                        ]);
                    }
                }
            }

            // CheckOut Logic



            $latestCheckIn->checkout_time = now();
            $latestCheckIn->update();

            // Updating Driver Status
            $driver->status = DriverStatus::Inactive->value;
            $driver->save();
            $driver = $driver->fresh();

            return sendResponse(200, 'success', 'Checkout Successfull', $driver);

            // Check If Driver Previously Checkout
        }catch(\Exception $e){
            return sendResponse(500, 'error', 'something went wrong', $e->getMessage());
        }
    }

    public function logout(Request $request) {
        try{
            // Check If Driver Status Is InActive
            $driver = $request->user();
            return $driver;

            if(!is_null($driver)){
                // * Check If User Already CheckedIn Today
                $latestCheckIn = getLastCheckIn();

                // CheckOut Logic
                if($latestCheckIn && !$latestCheckIn->checkout_time){
                    return sendResponse(400, 'error', 'Please checkout before logout.');
                }

                $driver->tokens()->delete();
            }


            return sendResponse(200, 'success', 'Logout Successfull');

            // Check If Driver Previously Checkout
        }catch(\Exception $e){
            return sendResponse(500, 'error', 'something went wrong', $e->getMessage());
        }
    }
}
