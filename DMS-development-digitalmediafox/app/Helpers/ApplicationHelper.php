<?php

use App\Enums\{FieldsTypes, Gender, MaritalStatus, OrderStatus, Range, Salutation, Status};
use App\Models\{Driver, DriverAttendance, Module, Order, Privilege};
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, Cache};

function translate($keyword): string
{
    return $keyword;
}


function getSalutations(){
    return Cache::rememberForever('salutations', function(){
        return Salutation::cases();
    });
}

function getMaritalStatuses(){
    return Cache::rememberForever('marital_statuses', function(){
        return MaritalStatus::cases();
    });
}

function getStatuses(){
    return Cache::rememberForever('statuses', function(){
        return Status::cases();
    });
}

function getGenders(){
    return Cache::rememberForever('genders', function(){
        return Gender::cases();
    });
}

function getFieldTypes(){
    return Cache::rememberForever('field_types', function(){
        return FieldsTypes::cases();
    });
}

function getRanges(){
    return Cache::rememberForever('ranges', function(){
        return Range::cases();
    });
}

function sendResponse($code, $status, $message, $data = []){
    return response()->json([
        'code' => $code,
        'status' => $status,
        'message' => $message,
        'response' => $data
    ], $code);
}

function getLastCheckIn()
{
    // Check if the driver has already checked in today without checking out
    return DriverAttendance::where('driver_id', request()->user()->id)
        ->whereNull('checkout_time') // Ensure checkout_time is null
        ->orderBy('id', 'desc')
        ->first();
}

function getLatestDroppedOrders()
{
        $lastCheckIn = getLastCheckIn();

        // If there's no last check-in, return an empty collection or handle accordingly
        if (!$lastCheckIn) {
            return []; // Return an empty collection or null based on your requirement
        }

        return Order::where([
            'driver_attendance_id' => $lastCheckIn->id,
            'status' => OrderStatus::Drop->value,
        ])
        ->with('business')
        ->selectRaw('business_id, COUNT(*) as total_dropped_orders')
        ->addSelect([
            'outstanding_cash' => function ($query) use ($lastCheckIn) {
                $query->selectRaw('SUM(amount_received - amount_paid)')
                    ->from('orders as sub_orders')
                    ->whereColumn('sub_orders.business_id', 'orders.business_id') // Correlate subquery to outer query
                    ->where('sub_orders.driver_attendance_id', $lastCheckIn->id) // Ensure subquery filters on attendance
                    ->where('sub_orders.status', OrderStatus::Drop->value); // Match the same conditions
            }
        ])
        ->groupBy('business_id')
        ->get();
}


function CheckPermission($name, $id)
{
    $permission = Privilege::where([$name => 1, 'module_id' => $id, 'role_id' => CheckUserRole()])->first();

    if(is_null($permission))
    {
        return 0;
    }
    else
    {
        return 1;
    }

}

 function CheckUserRole()
{
    if(Auth::user()->role_id == 0)
    {
        $id = 1;
    }
    else
    {
        $id = Auth::user()->role_id;
    }

    return $id;
}

function getModules(){
    return Module::with(['operations' => function($query){
        return $query->orderBy('index', 'asc');
    }])->whereNull('parent_id')->orderBy('index', 'asc')->get();
}

function getModulePrivilege($module_id){
    return Privilege::where(['module_id' => $module_id, 'role_id' => CheckUserRole(), 'is_view' => 1])->first();
}

function getLatestDriverId()
{
    // Retrieve the latest driver record based on driver_id
    $latestDriver = Driver::orderBy('id', 'desc')->first();

    if ($latestDriver && $latestDriver->driver_id) {
        // Extract numeric portion from driver_id
        $lastId = intval(str_replace('D', '', $latestDriver->driver_id));

        // Increment the numeric portion and format as DXXX
        $newId = 'D' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
    } else {
        // If no driver exists, start from D001
        $newId = 'D001';
    }

    return $newId;
}

function accessModule($Id)
{
    return Privilege::where(['module_id' => $Id, 'role_id' => auth()->user()->role_id])->first();
}
