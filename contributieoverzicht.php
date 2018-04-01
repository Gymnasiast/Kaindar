<?php

namespace Kaindar;

use Cyndaron\DBConnection;
use Cyndaron\Instelling;

$jaar = $_GET['jaar'] ?? Instelling::geefInstelling('jaar');

$pagina = new Pagina('Contributie-overzicht ' . $jaar);
$pagina->toonPrepagina();

?>
<form method="get" action="/contributieoverzicht">
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

$jaar = $_GET['jaar'] ?? Instelling::geefInstelling('jaar');
$iedereen = DBConnection::doQueryAndReturnFetchable("SELECT DISTINCT commentaar FROM mutaties WHERE (code='CON' AND DATE_FORMAT(datum, '%Y')=$jaar) OR (code='VBC$jaar') ORDER BY commentaar ASC;");
while ($persoon = $iedereen->fetch())
{
    echo '<h2>' . $persoon['commentaar'] . '</h2>';
    $contributies = DBConnection::doQueryAndReturnFetchable("SELECT DATE_FORMAT(datum, '%d-%m-%Y') AS datumf,bij-af AS tot FROM mutaties WHERE code='CON' AND commentaar=\"" . $persoon['commentaar'] . "\" AND DATE_FORMAT(datum, '%Y')=$jaar ORDER BY datum ASC;");
    echo 'Contributie: ';
    $aantalcontributies = $contributies->rowCount();
    $teller = 1;
    $tc = 0;
    while ($contributie = $contributies->fetch())
    {
        $tc += $contributie['tot'];
        echo '<abbr title="' . $contributie['datumf'] . '">' . Util::naarEuro($contributie['tot']) . '</abbr>';
        if ($teller != $aantalcontributies)
        {
            echo ' + ';
        }
        $teller++;
    }
    echo '<br />Totaal contributie: ' . Util::naarEuro($tc) . '<br /><br />';
    $vbcs = DBConnection::doQueryAndReturnFetchable("SELECT DATE_FORMAT(datum, '%d-%m-%Y') AS datumf,bij-af AS tot FROM mutaties WHERE code='VBC$jaar' AND commentaar=\"" . $persoon['commentaar'] . "\" ORDER BY datum ASC;");
    echo 'Vooruitbetaalde contributie: ';
    $aantalvbcs = $vbcs->rowCount();
    $teller = 1;
    $tv = 0;
    while ($vbc = $vbcs->fetch())
    {
        $tv += $vbc['tot'];
        echo '<abbr title="' . $vbc['datumf'] . '">' . Util::naarEuro($vbc['tot']) . '</abbr>';
        if ($teller != $aantalvbcs)
        {
            echo ' + ';
        }
        $teller++;
    }
    echo '<br />Totaal vooruitbetaalde contributie: ' . Util::naarEuro($tv) . '<br /><br />';
}

$pagina->toonPostPagina();