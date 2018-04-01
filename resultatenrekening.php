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
$jaar=eenregel("SELECT waarde FROM instellingen WHERE instelling='grootboekjaar' ;");
$bijschrijvingen=mysql_query("SELECT omschrijving, SUM(bij)-SUM(af) AS bedrag FROM codes,mutaties WHERE mutaties.code NOT IN (SELECT code FROM codes c WHERE (c.code LIKE \"C20%\" OR c.code LIKE \"D20%\" OR c.code LIKE \"VBC20%\")) AND codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=(SELECT waarde FROM instellingen WHERE instelling='grootboekjaar') GROUP BY omschrijving HAVING bedrag>=0 UNION SELECT omschrijving, SUM(bij)-SUM(af) AS bedrag FROM codes,mutaties WHERE (mutaties.code=\"C$jaar\" OR mutaties.code=\"D$jaar\" OR mutaties.code=\"VBC$jaar\") AND codes.code=mutaties.code GROUP BY omschrijving HAVING bedrag>0 ");
$afschrijvingen=mysql_query("SELECT omschrijving, SUM(af)-SUM(bij) AS bedrag FROM codes,mutaties WHERE mutaties.code NOT IN (SELECT code FROM codes c WHERE (c.code LIKE \"C20%\" OR c.code LIKE \"D20%\" OR c.code LIKE \"VBC20%\")) AND codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=(SELECT waarde FROM instellingen WHERE instelling='grootboekjaar') GROUP BY omschrijving HAVING bedrag>0 UNION SELECT omschrijving, SUM(af)-SUM(bij) AS bedrag FROM codes,mutaties WHERE (mutaties.code=\"C$jaar\" OR mutaties.code=\"D$jaar\" OR mutaties.code=\"VBC$jaar\") AND codes.code=mutaties.code GROUP BY omschrijving HAVING bedrag>0 ");

echo "<h1>$jaar</h1>";
echo 'Baten:<br /><table>';
$totaal=0;
while ($bijschrijvingentabel=mysql_fetch_assoc($bijschrijvingen))
{
	echo "<tr><td>" . $bijschrijvingentabel['omschrijving'] ."</td>";
	$totaal += $bijschrijvingentabel['bedrag'];
	echo "<td class=\"right\">&euro; " . number_format($bijschrijvingentabel['bedrag'], 2, ',', '.') ."</td></tr>";
}
echo '</table>Totaal: &euro; ' . number_format($totaal, 2, ',', '.') . '
<br /><br />Lasten:<br /><table>';
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
