<?php

namespace App\Enums;

enum MaritalStatus: string
{
    case Single   = 'Single';
    case Married  = 'Married';
    case Widower  = 'Widower';
    case Widow    = 'Widow';
    case Seprate  = 'Seprate';
    case Divorced = 'Divorced';
    case Engaged  = 'Engaged';
}
