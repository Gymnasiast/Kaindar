<?php

namespace Kaindar;

require_once('functies.php');

$jaar = $_GET['jaar'] ?? eenregel("SELECT waarde FROM instellingen WHERE instelling='jaar' ;");

$pagina = new Pagina('Staat van inkomsten en uitgaven ' . $jaar);
$pagina->toonPrepagina();

$bijschrijvingen = mysql_query("SELECT omschrijving, SUM(bij)-SUM(af) AS bedrag FROM codes,mutaties WHERE codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=(SELECT waarde FROM instellingen WHERE instelling='grootboekjaar') GROUP BY omschrijving HAVING bedrag>=0");
$afschrijvingen = mysql_query("SELECT omschrijving, SUM(af)-SUM(bij) AS bedrag FROM codes,mutaties WHERE codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=(SELECT waarde FROM instellingen WHERE instelling='grootboekjaar') GROUP BY omschrijving HAVING bedrag>0");

echo '<h2>Inkomsten</h2>
<table class="table table-bordered table-striped table-notstretched">';
$totaal = 0;
while ($bijschrijvingentabel = mysql_fetch_assoc($bijschrijvingen))
{
    echo "<tr><td>" . $bijschrijvingentabel['omschrijving'] . "</td>";
    $totaal += $bijschrijvingentabel['bedrag'];
    echo "<td class=\"text-right\">&euro; " . number_format($bijschrijvingentabel['bedrag'], 2, ',', '.') . "</td></tr>";
}
echo '</table>Totaal: ' . naarEuro($totaal) . '
<br />
<h2>Uitgaven</h2>
<table class="table table-bordered table-striped table-notstretched">';
$totaal = 0;
while ($afschrijvingentabel = mysql_fetch_assoc($afschrijvingen))
{
    echo "<tr><td>" . $afschrijvingentabel['omschrijving'] . "</td>";
    $totaal += $afschrijvingentabel['bedrag'];
    echo "<td class=\"text-right\">&euro; " . number_format($afschrijvingentabel['bedrag'], 2, ',', '.') . "</td></tr>";
}
echo '</table>Totaal: ' . naarEuro($totaal);

$pagina->toonPostPagina();