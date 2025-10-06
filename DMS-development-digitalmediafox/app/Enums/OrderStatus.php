<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pickup = 'Pickup';
    case Drop = 'Drop';
    case Cancel = 'Cancel';
}
