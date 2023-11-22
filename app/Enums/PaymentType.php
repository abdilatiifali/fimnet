<?php

namespace App\Enums;

enum PaymentType: String
{
    case cash = 'cash';
    case mpesa = 'mpesa';
    case _ = '_';
}
