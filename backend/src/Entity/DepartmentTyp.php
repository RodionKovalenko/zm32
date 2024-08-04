<?php

namespace App\Entity;

enum DepartmentTyp: int
{
    case ALLE = 0;
    case ARBEITSVORBEREITUNG = 1;
    case EDELMETALL = 2;
    case KUNSTSTOFF = 3;
    case CADCAM = 4;
    case ALLGEMEIN = 5;
    case PROTHETIK = 6;

    public function getName(): string
    {
        return match ($this) {
            self::ARBEITSVORBEREITUNG => 'Arbeitsvorbereitung',
            self::EDELMETALL => 'Edelmetall',
            self::KUNSTSTOFF => 'Kunststoff',
            self::PROTHETIK => 'Prothetik',
            self::CADCAM => 'CADCAM',
        };
    }

    public static function getByName(string $name): self
    {
        switch ($name) {
            case 'Arbeitsvorbereitung':
                return self::ARBEITSVORBEREITUNG;
            case 'Edelmetall':
                return self::EDELMETALL;
            case 'Kunststoff':
                return self::KUNSTSTOFF;
            case 'Prothetik':
                return self::PROTHETIK;
            case 'CADCAM':
                return self::CADCAM;
        }
    }
}
