<?php

// src/Enum/PaymentStatus.php
namespace App\Enum;

enum PaymentStatus: string
{
    case PAYE = 'payé';
    case NON_PAYE = 'non payé';
    case EN_COURS = 'en cours';

}
?>