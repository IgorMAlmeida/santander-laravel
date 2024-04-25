<?php

namespace App\Adapters;

class ReplaceValues
{
    public function replaceComma($str) : string
    {
        $value = str_replace('.', '', $str);
        $value = str_replace(',', '.', $value);
        return $value;
    }
}
