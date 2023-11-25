<?php

namespace App\Enums;

enum CustomerStatus: String
{
    case active = 'active';
    case blocked = 'blocked';
    case new = 'new';
}
