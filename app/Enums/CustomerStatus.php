<?php

namespace App\Enums;

enum CustomerStatus: Int
{
    case active = 1;
    case blocked = 2;
    case new = 3;
}
