<?php

namespace App\Enums;

enum DriverStatus: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
    case Busy = 'Busy';
    case Blocked = 'Blocked';
}
