<?php

namespace Kaindar;

require_once('functies.php');

$jaar = $_GET['jaar'] ?? eenregel('SELECT waarde FROM instellingen WHERE instelling="jaar" ;');

$pagina = new Pagina('Contributie-overzicht ' . $jaar);
$pagina->toonPrepagina();


?>
    <form method="get" action="/contributieoverzicht">
        Jaar: <select name="jaar">
            <?php
            foreach (geefAlleJaren() as $teller)
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

$jaar = $_GET['jaar'];
$iedereen = mysql_query("SELECT DISTINCT commentaar FROM mutaties WHERE (code='CON' AND DATE_FORMAT(datum, '%Y')=$jaar) OR (code='VBC$jaar') ORDER BY commentaar ASC;");
while ($persoon = mysql_fetch_assoc($iedereen))
{
    echo '<h2>' . $persoon['commentaar'] . '</h2>';
    $contributies = mysql_query("SELECT DATE_FORMAT(datum, '%d-%m-%Y') AS datumf,bij-af AS tot FROM mutaties WHERE code='CON' AND commentaar=\"" . $persoon['commentaar'] . "\" AND DATE_FORMAT(datum, '%Y')=$jaar ORDER BY datum ASC;");
    echo 'Contributie: ';
    $aantalcontributies = mysql_num_rows($contributies);
    $teller = 1;
    $tc = 0;
    while ($contributie = mysql_fetch_assoc($contributies))
    {
        $tc += $contributie['tot'];
        echo '<abbr title="' . $contributie['datumf'] . '">&euro; ' . number_format($contributie['tot'], 2, ',', '.') . '</abbr>';
        if ($teller != $aantalcontributies)
        {
            echo ' + ';
        }
        $teller++;
    }
    echo '<br />Totaal contributie: &euro; ' . number_format($tc, 2, ',', '.') . '<br /><br />';
    $vbcs = mysql_query("SELECT DATE_FORMAT(datum, '%d-%m-%Y') AS datumf,bij-af AS tot FROM mutaties WHERE code='VBC$jaar' AND commentaar=\"" . $persoon['commentaar'] . "\" ORDER BY datum ASC;");
    echo 'Vooruitbetaalde contributie: ';
    $aantalvbcs = mysql_num_rows($vbcs);
    $teller = 1;
    $tv = 0;
    while ($vbc = mysql_fetch_assoc($vbcs))
    {
        $tv += $vbc['tot'];
        echo '<abbr title="' . $vbc['datumf'] . '">&euro; ' . number_format($vbc['tot'], 2, ',', '.') . '</abbr>';
        if ($teller != $aantalvbcs)
        {
            echo ' + ';
        }
        $teller++;
    }
    echo '<br />Totaal vooruitbetaalde contributie: &euro; ' . number_format($tv, 2, ',', '.') . '<br /><br />';
}

$pagina->toonPostPagina();