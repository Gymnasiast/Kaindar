<?php
namespace Kaindar;

use Cyndaron\DBConnection;

class Util
{
    const MAANDEN = [
        '',
        'Januari',
        'Februari',
        'Maart',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Augustus',
        'September',
        'Oktober',
        'November',
        'December',
    ];

    public static function geefMaandnaam(int $maandnummer)
    {
        if ($maandnummer < 1 || $maandnummer > 12)
        {
            return self::MAANDEN[0];
        }

        return self::MAANDEN[$maandnummer];
    }

    public static function naarEuro(float $bedrag): string
    {
        return '&euro;&nbsp;' . number_format($bedrag, 2, ',', '.');
    }

    public static function geefHuidigSaldo(string $rekening = '')
    {
        $rekeningstring = '';
        if ($rekening)
        {
            $rekeningstring=" WHERE rekening='$rekening'";
        }
        return DBConnection::doQueryAndFetchOne('SELECT SUM(bij)-SUM(af) FROM mutaties' . $rekeningstring);
    }

    public static function geefAlleJaren(string $where = ''): array
    {
        $pdo = DBConnection::getPdo();
        $prep = $pdo->prepare('SELECT DISTINCT DATE_FORMAT(datum, \'%Y\') AS jaar FROM mutaties ' . $where . ' ORDER BY jaar DESC');
        $prep->execute([]);

        $jaren = [];
        while ($jaar = $prep->fetchColumn())
        {
            $jaren[] = $jaar;
        }

        return $jaren;
    }

    public static function geefAlleRekeningen(): array
    {
        return DBConnection::doQueryAndFetchAll('SELECT * FROM rekeningen;');
    }
}