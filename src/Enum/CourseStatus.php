<?php
// src/Enum/CourseStatus.php
namespace App\Enum;

enum CourseStatus: string
{
    case PROGRAMME = 'programmé';
    case FINI = 'fini';
    case ANNULE = 'annulé';

}

?>