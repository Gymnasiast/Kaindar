<?php

function geefMaandnaam($maandnummer)
{
	$maandnummer=ltrim($maandnummer, '0');
	if (!is_numeric((int)$maandnummer) || $maandnummer<1 || $maandnummer>12)
	{
		return null;
	}
	$maanden[1]='Januari';
	$maanden[2]='Februari';
	$maanden[3]='Maart';
	$maanden[4]='April';
	$maanden[5]='Mei';
	$maanden[6]='Juni';
	$maanden[7]='Juli';
	$maanden[8]='Augustus';
	$maanden[9]='September';
	$maanden[10]='Oktober';
	$maanden[11]='November';
	$maanden[12]='December';
	return $maanden[$maandnummer];
}

function naarEuro($bedrag)
{
	return '&euro;&nbsp;'.number_format($bedrag, 2, ',', '.');
}

function geefHuidigSaldo($rekening='')
{
	$rekeningstring='';
	if($rekening)
	{
		$rekeningstring=" WHERE rekening='$rekening'";
	}
	return eenregel('SELECT SUM(bij)-SUM(af) FROM mutaties'.$rekeningstring.';');
}
?>
