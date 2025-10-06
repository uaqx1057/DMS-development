<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Vehicle;
use App\Models\User;
class VehicleMaintenancesReport extends Model
{
    use HasFactory;

    protected $table = 'vehicle_maintenances_report';

    protected $fillable = [
    'vehicle_id',
    'maintenance_type',
    'description',
    'urgency',
    'status',
    'request_by',
    'created_at',
    'updated_at',
];


    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'request_by');
    }
}


?>