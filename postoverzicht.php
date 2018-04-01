<?php
namespace Kaindar;

use Cyndaron\DBConnection;
use Cyndaron\Instelling;

$afkorting = $_GET['afkorting'] ?? '';
$jaar = $_GET['jaar'] ?? Instelling::geefInstelling('jaar');

if ($afkorting)
{
    $jaren = Util::geefAlleJaren('WHERE rekening="' . $afkorting . '"');
    $andString = ' AND rekening="' . $afkorting . '"';
    $rekening = DBConnection::doQueryAndFetchOne('SELECT omschrijving FROM rekeningen WHERE afkorting=?;', [$afkorting]);
}
else
{
    $jaren = Util::geefAlleJaren();
    $andString = '';
    $rekening = 'Alle rekeningen';
}

$posten = DBConnection::doQueryAndReturnFetchable('SELECT DISTINCT m.code, omschrijving, SUM(bij), SUM(af), SUM(ROUND((bij*(btw/(100+btw))), 2)) AS "Ontvangen BTW", SUM(ROUND((af*(btw/(100+btw))), 2)) AS "Betaalde BTW" FROM mutaties m, codes c WHERE m.code=c.code AND DATE_FORMAT(datum, \'%Y\')=' . $jaar . $andString . ' GROUP BY m.code ORDER BY omschrijving ASC;');

$pagina = new Pagina('Opgetelde posten');
$pagina->toonPrepagina();

printf('Opgetelde posten van %s van het jaar %d.', $rekening, $jaar);

if (count($jaren) === 0)
{
    die ("<br />Er is nog niets ingevoerd voor deze rekening.</body></html>");
}
?>
<form method="get" action="/postoverzicht">
    <select name="jaar">
        <?php

        foreach ($jaren as $teller)
        {
            echo "<option";
            if ($teller == $jaar)
            {
                echo " selected";
            }
            echo " value=\"$teller\">$teller</option>";
        }
        ?>
    </select>
    <input type="hidden" value="<?=$afkorting;?>" name="afkorting"/>
    <input type="submit" value="Weergeven"/>
</form>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Post</th>
            <th>Totaal bij</th>
            <th>Totaal af</th>
            <th>Cashflow</th>
            <th>Ontv. BTW</th>
            <th>Bet. BTW</th>
        </tr>
    </thead>
    <?php
    while (list($code, $omschrijving, $bij, $af, $ontvbtw, $betbtw) = $posten->fetch())
    {
        $bijminaf = Util::naarEuro($bij - $af);
        $bij = Util::naarEuro($bij);
        $af = Util::naarEuro($af);
        $ontvbtw = Util::naarEuro($ontvbtw);
        $betbtw = Util::naarEuro($betbtw);
        echo "<tr><td>$omschrijving</td><td class=\"text-right\">$bij</td><td class=\"text-right\">$af</td><td class=\"text-right\">$bijminaf</td><td class=\"text-right\">$ontvbtw</td><td class=\"text-right\">$betbtw</td></tr>";
    }
    ?>
</table>
<?php
$pagina->toonPostPagina();
