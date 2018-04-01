<?php
require_once('functies.php');
echo '
<html>
<head>
<link href="stijl.css" rel="stylesheet" type="text/css" />
</head>
<body>
<p>
<a href="/">Terug naar het hoofdmenu</a>
</p>
';
$bijschrijvingen=mysql_query("SELECT omschrijving, SUM(bij)-SUM(af) AS bedrag FROM codes,mutaties WHERE codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=(SELECT waarde FROM instellingen WHERE instelling='grootboekjaar') GROUP BY omschrijving HAVING bedrag>=0");
$afschrijvingen=mysql_query("SELECT omschrijving, SUM(af)-SUM(bij) AS bedrag FROM codes,mutaties WHERE codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=(SELECT waarde FROM instellingen WHERE instelling='grootboekjaar') GROUP BY omschrijving HAVING bedrag>0");
$jaar=eenregel("SELECT waarde FROM instellingen WHERE instelling='grootboekjaar' ;");
echo "<h1>$jaar</h1>";
echo 'Ontvangsten:<br /><table>';
$totaal=0;
while ($bijschrijvingentabel=mysql_fetch_assoc($bijschrijvingen))
{
	echo "<tr><td>" . $bijschrijvingentabel['omschrijving'] ."</td>";
	$totaal += $bijschrijvingentabel['bedrag'];
	echo "<td class=\"right\">&euro; " . number_format($bijschrijvingentabel['bedrag'], 2, ',', '.') ."</td></tr>";
}
echo '</table>Totaal: &euro; ' . number_format($totaal, 2, ',', '.') . '
<br /><br />Uitgaven:<br /><table>';
$totaal=0;
while ($afschrijvingentabel=mysql_fetch_assoc($afschrijvingen))
{
	echo "<tr><td>" . $afschrijvingentabel['omschrijving'] ."</td>";
	$totaal += $afschrijvingentabel['bedrag'];
	echo "<td class=\"right\">&euro; " . number_format($afschrijvingentabel['bedrag'], 2, ',', '.') ."</td></tr>";
}
echo '</table>Totaal: &euro; ' . number_format($totaal, 2, ',', '.');
?>
</body>
</html>
