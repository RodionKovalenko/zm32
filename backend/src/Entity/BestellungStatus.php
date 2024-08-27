<?php

namespace App\Entity;

enum BestellungStatus: int
{
    case OFFEN = 1;
    case BESTELLT = 2;
    case GELIEFERT = 3;
    case STORNIERT = 4;

    public static function getStatusString(int $status): string
    {
        switch ($status) {
            case self::OFFEN->value:
                return 'Offen';
            case self::BESTELLT->value:
                return 'Bestellt';
            case self::GELIEFERT->value:
                return 'Geliefert';
            case self::STORNIERT->value:
                return 'Storniert';
            default:
                return 'Unbekannt';
        }
    }
}