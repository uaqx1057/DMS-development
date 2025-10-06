<?php

namespace App\Enums;

enum FuelRequestStatus: string
{
    case Pending = 'Pending';
    case Verified = 'Verified';
    case Approved = 'Approved';
    case Rejected = 'Rejected';
}
