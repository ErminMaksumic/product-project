<?php

namespace App\StateMachine\Enums;

enum ProductActions: string
{
    case DraftToActive = 'DraftToActive';
    case ActiveToDelete = 'ActiveToDelete';
    case ActiveToDraft = 'ActiveToDraft';
}
