<?php

namespace App\Entity;

enum BestellungStatus: int
{
    case OFFEN = 1;
    case BESTELLT = 2;
    case GELIEFERT = 3;
}