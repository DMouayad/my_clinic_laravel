<?php

namespace Tests\Utils\Enums;

enum UserRole: string
{
    case admin = "admin";
    case dentist = "dentist";
    case secretary = "secretary";
    case patient = "patient";
    case invalid = "invalid_role";
}
