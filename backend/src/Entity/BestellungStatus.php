<?php

namespace App\Entity;

enum BestellungStatus: int
{
    case OFFEN = 1;
    case BESTELLT = 2;
    case GELIEFERT = 3;

    public static function getStatusString(int $status): string
    {
        switch ($status) {
            case self::OFFEN->value:
                return 'Offen';
            case self::BESTELLT->value:
                return 'Bestellt';
            case self::GELIEFERT->value:
                return 'Geliefert';
            default:
                return 'Unbekannt';
        }
    }
}