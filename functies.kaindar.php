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

function naarEuro($bedrag)
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
