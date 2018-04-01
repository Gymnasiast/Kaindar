<?php
namespace Kaindar;

class Util
{
    public static function naarEuro(float $bedrag): string
    {
        return '&euro;&nbsp;' . number_format($bedrag, 2, ',', '.');
    }
}