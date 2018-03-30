<?php
require_once('functies.php');
$afkorting = $_GET['afkorting'] ?? '';
if (!$afkorting)
{
	$rekeningstring='';
}
else
{
	$rekeningstring="WHERE rekening='$afkorting' ";
}

$overzichten=mysql_query("SELECT SUM(bij)-SUM(af) AS cashflow,DATE_FORMAT(datum,'%Y') AS jaar,DATE_FORMAT(datum,'%m') AS maand FROM mutaties $rekeningstring GROUP BY jaar,maand ORDER BY jaar DESC, maand DESC;");

$laatstejaar=0;
$saldo=geefHuidigSaldo($afkorting);
?>
<html>
<head>
<link href="stijl.css" rel="stylesheet" type="text/css" />
</head>
<body>
<p>
<a href="index.php">Terug naar het hoofdmenu</a>
</p>
<table>
<?php
while ($overzicht=mysql_fetch_assoc($overzichten))
{
	if ($laatstejaar!=$overzicht['jaar'])
	{
		$laatstejaar=$overzicht['jaar'];
		echo '<tr><td colspan="100%"><h1>'.$overzicht['jaar'].'</h1></td></tr>';
	}

	echo '<tr><td>'.geefMaandnaam($overzicht['maand']).'</td><td>'.naarEuro($overzicht['cashflow']).'</td><td>'.naarEuro($saldo).'</td></tr>';
	$saldo=$saldo-$overzicht['cashflow'];
}
?>
</table>
</body>
</html>
