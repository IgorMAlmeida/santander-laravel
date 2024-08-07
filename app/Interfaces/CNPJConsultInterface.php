<?php

namespace App\Interfaces;

interface CNPJConsultInterface
{
    public function consultCnpj(string $cnpj):array;
}