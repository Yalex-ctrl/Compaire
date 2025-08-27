<?php

namespace App\Enum;

enum AutoStatusEnum: string
{
    case YES = 'yes';
    case NO = 'no';
    case IN_PROGRESS = 'in_progress';

    public function label(): string
    {
        return match($this) {
            self::YES => 'Oui',
            self::NO => 'Non',
            self::IN_PROGRESS => 'En cours',
        };
    }

    public static function choices(): array
    {
        return [
            'Oui' => self::YES,
            'Non' => self::NO,
            'En cours' => self::IN_PROGRESS,
        ];
    }

    public static function fromLabel(string $label): ?self
    {
        return match($label) {
            'Oui' => self::YES,
            'Non' => self::NO,
            'En cours' => self::IN_PROGRESS,
            default => null,
        };
    }
}
