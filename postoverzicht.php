<?php
namespace Kaindar;

require_once('functies.php');
$afkorting = $_GET['afkorting'] ?? '';
$jaar = $_GET['jaar'] ?? eenregel("SELECT waarde FROM instellingen WHERE instelling=\"jaar\" ;");;

if ($afkorting)
{
    $jaren = geefAlleJaren('WHERE rekening="' . $afkorting . '"');
    $andString = ' AND rekening="' . $afkorting . '"';
    $rekening = eenregel("SELECT omschrijving FROM rekeningen WHERE afkorting=\"$afkorting\" ;");
}
else
{
    $jaren = geefAlleJaren();
    $andString = '';
    $rekening = 'Alle rekeningen';
}

$posten = mysql_query('SELECT DISTINCT m.code, omschrijving, SUM(bij), SUM(af), SUM(ROUND((bij*(btw/(100+btw))), 2)) AS "Ontvangen BTW", SUM(ROUND((af*(btw/(100+btw))), 2)) AS "Betaalde BTW" FROM mutaties m, codes c WHERE m.code=c.code AND DATE_FORMAT(datum, \'%Y\')=' . $jaar . $andString . ' GROUP BY m.code ORDER BY omschrijving ASC;');

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
    while (list($code, $omschrijving, $bij, $af, $ontvbtw, $betbtw) = mysql_fetch_row($posten))
    {
        $bijminaf = number_format($bij - $af, 2, ',', '.');
        $bij = number_format($bij, 2, ',', '.');
        $af = number_format($af, 2, ',', '.');
        $ontvbtw = number_format($ontvbtw, 2, ',', '.');
        $betbtw = number_format($betbtw, 2, ',', '.');
        echo "<tr><td>$omschrijving</td><td class=\"text-right\">&euro; $bij</td><td class=\"text-right\">&euro; $af</td><td class=\"text-right\">&euro; $bijminaf</td><td class=\"text-right\">&euro; $ontvbtw</td><td class=\"text-right\">&euro; $betbtw</td></tr>";
    }
    ?>
</table>
<?php
$pagina->toonPostPagina();
