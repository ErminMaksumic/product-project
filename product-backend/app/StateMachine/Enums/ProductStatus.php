<?php

namespace App\StateMachine\Enums;

enum ProductStatus: string {
    case ACTIVATED = 'ACTIVATED';
    case DELETED = 'DELETED';
    case DRAFT = 'DRAFT';
}
