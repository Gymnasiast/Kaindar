<?php

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

function geefMaandnaam(int $maandnummer)
{
	if ($maandnummer < 1 || $maandnummer > 12)
	{
		return MAANDEN[0];
	}

	return MAANDEN[$maandnummer];
}

function naarEuro(float $bedrag)
{
	return '&euro;&nbsp;' . number_format($bedrag, 2, ',', '.');
}

function geefHuidigSaldo(string $rekening = '')
{
	$rekeningstring = '';
	if ($rekening)
	{
		$rekeningstring=" WHERE rekening='$rekening'";
	}
	return eenregel('SELECT SUM(bij)-SUM(af) FROM mutaties' . $rekeningstring.';');
}

function geefAlleJaren(string $where = ''): array
{
    global $pdo;
    $prep = $pdo->prepare('SELECT DISTINCT DATE_FORMAT(datum, \'%Y\') AS jaar FROM mutaties ' . $where . ' ORDER BY jaar DESC');
    $prep->execute([]);

    $jaren = [];
    while ($jaar = $prep->fetchColumn())
    {
        $jaren[] = $jaar;
    }

    return $jaren;
}

function geefAlleRekeningen(): array
{
    global $pdo;
    $prep = $pdo->prepare('SELECT * FROM rekeningen;');
    $prep->execute([]);

    return $prep->fetchAll();
}
