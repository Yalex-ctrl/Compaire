<?php
// src/Enum/AccountReportEnum.php
namespace App\Enum;

enum AccountReportEnum: string
{
    case YES = 'yes';
    case NO = 'no';

    public function getLabel(): string
    {
        return match($this) {
            self::YES => 'Yes',
            self::NO => 'No',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::YES => 'green',
            self::NO => 'red',
        };
    }
}
