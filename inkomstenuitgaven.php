<?php
namespace Kaindar;

use Cyndaron\DBConnection;
use Cyndaron\Instelling;

$jaar = $_GET['jaar'] ?? Instelling::geefInstelling('jaar');

$pagina = new Pagina('Staat van inkomsten en uitgaven ' . $jaar);
$pagina->toonPrepagina();

$bijschrijvingen = DBConnection::doQueryAndReturnFetchable("SELECT omschrijving, SUM(bij)-SUM(af) AS bedrag FROM codes,mutaties WHERE codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=? GROUP BY omschrijving HAVING bedrag>=0", [$jaar]);
$afschrijvingen = DBConnection::doQueryAndReturnFetchable("SELECT omschrijving, SUM(af)-SUM(bij) AS bedrag FROM codes,mutaties WHERE codes.iskruispost=0 AND codes.code=mutaties.code AND DATE_FORMAT(datum, '%Y')=? GROUP BY omschrijving HAVING bedrag>0", [$jaar]);

?>
<form method="get" action="/inkomstenuitgaven">
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

echo '<h2>Inkomsten</h2>
<table class="table table-bordered table-striped table-notstretched">';
$totaal = 0;
while ($bijschrijvingentabel = $bijschrijvingen->fetch())
{
    echo "<tr><td>" . $bijschrijvingentabel['omschrijving'] . "</td>";
    $totaal += $bijschrijvingentabel['bedrag'];
    echo "<td class=\"text-right\">" . Util::naarEuro($bijschrijvingentabel['bedrag']) . "</td></tr>";
}
echo '</table>Totaal: ' . Util::naarEuro($totaal) . '
<br />
<h2>Uitgaven</h2>
<table class="table table-bordered table-striped table-notstretched">';
$totaal = 0;
while ($afschrijvingentabel = $afschrijvingen->fetch())
{
    echo "<tr><td>" . $afschrijvingentabel['omschrijving'] . "</td>";
    $totaal += $afschrijvingentabel['bedrag'];
    echo "<td class=\"text-right\">" . Util::naarEuro($afschrijvingentabel['bedrag']) . "</td></tr>";
}
echo '</table>Totaal: ' . Util::naarEuro($totaal);

$pagina->toonPostPagina();