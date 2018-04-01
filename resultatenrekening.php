<?php
namespace Kaindar;
require_once('functies.php');

$jaar = $_GET['jaar'] ?? eenregel("SELECT waarde FROM instellingen WHERE instelling='jaar' ;");

$pagina = new Pagina('Resultatenrekening ' . $jaar);
$pagina->toonPrepagina();

$bijschrijvingen=mysql_query("SELECT omschrijving, SUM(bij)-SUM(af) AS bedrag FROM codes,mutaties WHERE mutaties.code NOT IN (SELECT code FROM codes c WHERE (c.code LIKE \"C20%\" OR c.code LIKE \"D20%\" OR c.code LIKE \"VBC20%\")) AND codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=(SELECT waarde FROM instellingen WHERE instelling='grootboekjaar') GROUP BY omschrijving HAVING bedrag>=0 UNION SELECT omschrijving, SUM(bij)-SUM(af) AS bedrag FROM codes,mutaties WHERE (mutaties.code=\"C$jaar\" OR mutaties.code=\"D$jaar\" OR mutaties.code=\"VBC$jaar\") AND codes.code=mutaties.code GROUP BY omschrijving HAVING bedrag>0 ");
$afschrijvingen=mysql_query("SELECT omschrijving, SUM(af)-SUM(bij) AS bedrag FROM codes,mutaties WHERE mutaties.code NOT IN (SELECT code FROM codes c WHERE (c.code LIKE \"C20%\" OR c.code LIKE \"D20%\" OR c.code LIKE \"VBC20%\")) AND codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=(SELECT waarde FROM instellingen WHERE instelling='grootboekjaar') GROUP BY omschrijving HAVING bedrag>0 UNION SELECT omschrijving, SUM(af)-SUM(bij) AS bedrag FROM codes,mutaties WHERE (mutaties.code=\"C$jaar\" OR mutaties.code=\"D$jaar\" OR mutaties.code=\"VBC$jaar\") AND codes.code=mutaties.code GROUP BY omschrijving HAVING bedrag>0 ");

echo '<h2>Baten</h2>
<table class="table table-bordered table-striped table-notstretched">';
$totaal=0;
while ($bijschrijvingentabel=mysql_fetch_assoc($bijschrijvingen))
{
	echo "<tr><td>" . $bijschrijvingentabel['omschrijving'] ."</td>";
	$totaal += $bijschrijvingentabel['bedrag'];
	echo "<td class=\"text-right\">" . naarEuro($bijschrijvingentabel['bedrag']) ."</td></tr>";
}
echo '</table><b>Totaal: ' . naarEuro($totaal) . '</b><br /><br />
<h2>Lasten</h2>
<table class="table table-bordered table-striped table-notstretched">';
$totaal=0;
while ($afschrijvingentabel=mysql_fetch_assoc($afschrijvingen))
{
	echo "<tr><td>" . $afschrijvingentabel['omschrijving'] ."</td>";
	$totaal += $afschrijvingentabel['bedrag'];
	echo "<td class=\"text-right\">" . naarEuro($afschrijvingentabel['bedrag']) ."</td></tr>";
}
echo '</table><b>Totaal: ' . naarEuro($totaal) . '</b>';

$pagina->toonPostPagina();