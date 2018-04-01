<?php

namespace Kaindar;

use Cyndaron\DBConnection;
use Cyndaron\Instelling;

$jaar = $_GET['jaar'] ?? Instelling::geefInstelling('jaar');

$pagina = new Pagina('Resultatenrekening ' . $jaar);
$pagina->toonPrepagina();

$bijschrijvingen = DBConnection::doQueryAndReturnFetchable("SELECT omschrijving, SUM(bij)-SUM(af) AS bedrag FROM codes,mutaties WHERE mutaties.code NOT IN (SELECT code FROM codes c WHERE (c.code LIKE \"C20%\" OR c.code LIKE \"D20%\" OR c.code LIKE \"VBC20%\")) AND codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=(SELECT waarde FROM instellingen WHERE naam='jaar') GROUP BY omschrijving HAVING bedrag>=0 UNION SELECT omschrijving, SUM(bij)-SUM(af) AS bedrag FROM codes,mutaties WHERE (mutaties.code=? OR mutaties.code=? OR mutaties.code=?) AND codes.code=mutaties.code GROUP BY omschrijving HAVING bedrag>0", ["C$jaar", "D$jaar", "VBC$jaar"]);
$afschrijvingen = DBConnection::doQueryAndReturnFetchable("SELECT omschrijving, SUM(af)-SUM(bij) AS bedrag FROM codes,mutaties WHERE mutaties.code NOT IN (SELECT code FROM codes c WHERE (c.code LIKE \"C20%\" OR c.code LIKE \"D20%\" OR c.code LIKE \"VBC20%\")) AND codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=(SELECT waarde FROM instellingen WHERE naam='jaar') GROUP BY omschrijving HAVING bedrag>0 UNION SELECT omschrijving, SUM(af)-SUM(bij) AS bedrag FROM codes,mutaties WHERE (mutaties.code=? OR mutaties.code=? OR mutaties.code=?) AND codes.code=mutaties.code GROUP BY omschrijving HAVING bedrag>0", ["C$jaar", "D$jaar", "VBC$jaar"]);

?>
<form method="get" action="/resultatenrekening">
    Jaar: <select name="jaar">
        <?php
        foreach (Util::geefAlleJaren() as $teller)
        {
            echo '<option';
            if ($jaar == $teller)
            {
                echo ' selected';
            }
            echo ' name="' . $teller . '">' . $teller . '</option>';
        }
        ?>
    </select>

    <input type="submit" value="Bekijken"/>
</form>
<?php

echo '<h2>Baten</h2>
<table class="table table-bordered table-striped table-notstretched">';
$totaal = 0;
while ($bijschrijvingentabel = $bijschrijvingen->fetch())
{
    echo "<tr><td>" . $bijschrijvingentabel['omschrijving'] . "</td>";
    $totaal += $bijschrijvingentabel['bedrag'];
    echo "<td class=\"text-right\">" . Util::naarEuro($bijschrijvingentabel['bedrag']) . "</td></tr>";
}
echo '</table><b>Totaal: ' . Util::naarEuro($totaal) . '</b><br /><br />
<h2>Lasten</h2>
<table class="table table-bordered table-striped table-notstretched">';
$totaal = 0;
while ($afschrijvingentabel = $afschrijvingen->fetch())
{
    echo "<tr><td>" . $afschrijvingentabel['omschrijving'] . "</td>";
    $totaal += $afschrijvingentabel['bedrag'];
    echo "<td class=\"text-right\">" . Util::naarEuro($afschrijvingentabel['bedrag']) . "</td></tr>";
}
echo '</table><b>Totaal: ' . Util::naarEuro($totaal) . '</b>';

$pagina->toonPostPagina();