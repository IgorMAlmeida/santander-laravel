<?php

namespace App\Enums;

use App\Services\CasaDosDados;

enum CNPJClassConsultEnum: string
{
    case CasaDosDados = 'CasaDosDados';

    public static function chooseClass(string $siteConsult): object
    {
        $enum = self::from($siteConsult);

        return match($enum) {
            self::CasaDosDados => new CasaDosDados(),
            default => new CasaDosDados(),
        };
    }

}