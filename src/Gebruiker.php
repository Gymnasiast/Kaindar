<?php
namespace Kaindar;

class Gebruiker
{
    public static function isAdmin()
    {
        return true;
    }

    public static function nieuweMelding(string $tekst)
    {
        $_SESSION['meldingen'][] = $tekst;
    }

    public static function geefMeldingen()
    {
        $return = @$_SESSION['meldingen'];
        $_SESSION['meldingen'] = null;
        return $return;
    }
}