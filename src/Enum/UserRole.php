<?php

namespace App\Enum;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case INSTRUCTOR = 'ROLE_INSTRUCTOR';
    case STUDENT = 'ROLE_STUDENT';
}
