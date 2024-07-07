<?php

namespace App\Entity;

enum DepartmentTyp: int
{
    case ARBEITSVORBEREITUNG = 1;
    case EDELMETALL = 2;
    case KUNSTSTOFF = 3;
    case CADCAM = 4;

    public function getName(): string
    {
        return match ($this) {
            self::ARBEITSVORBEREITUNG => 'Arbeitsvorbereitung',
            self::EDELMETALL => 'Edelmetall',
            self::KUNSTSTOFF => 'Kunststoff',
            self::CADCAM => 'CadCaM',
        };
    }
}
