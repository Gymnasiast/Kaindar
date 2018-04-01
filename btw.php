<?php
namespace Kaindar;

require_once('functies.php');
$huidigJaar = $_POST['jaar'] ?? eenregel('SELECT waarde FROM instellingen WHERE instelling="jaar" ;');

$btwbij = mysql_query("SELECT DISTINCT btw, SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE bij<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar GROUP BY btw ;");
$btwaf = mysql_query("SELECT DISTINCT btw, SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE af<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar GROUP BY btw ;");
$btwbijttlz0 = mysql_query("SELECT SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE btw<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar ;");
$btwafttlz0 = mysql_query("SELECT SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE btw<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar ;");
$btwbijttlm0 = mysql_query("SELECT SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar ;");
$btwafttlm0 = mysql_query("SELECT SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar ;");

$pagina = new Pagina('BTW-overzicht');
$pagina->toonPrepagina();

$jaren = geefAlleJaren();
?>

<form method="post" action="/btw">
    <select name="jaar">
        <?php
        foreach ($jaren as $jaar)
        {
            echo "<option";
            if ($jaar == $huidigJaar)
            {
                echo " selected";
            }
            echo " value=\"$jaar\">$jaar</option>";
        }

        ?>
    </select>
    <input type="submit" value="Weergeven"/>
</form>
<h1>Ontvangen BTW</h1>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Tarief</th>
            <th>Incl.</th>
            <th>Excl.</th>
            <th>BTW</th>
        </tr>
    </thead>

    <?php
    while (list($tarief, $incl, $excl, $btw) = mysql_fetch_row($btwbij))
    {
        printf('<tr><td>%s%%</td><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            $tarief,
            naarEuro($incl),
            naarEuro($excl),
            naarEuro($btw)
        );
    }
    while (list($incl, $excl, $btw) = mysql_fetch_row($btwbijttlm0))
    {
        printf('<tr><td>Totaal met 0%%</td class="text-right"><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            naarEuro($incl),
            naarEuro($excl),
            naarEuro($btw)
        );
    }
    while (list($incl, $excl, $btw) = mysql_fetch_row($btwbijttlz0))
    {
        printf('<tr><td>Totaal zonder 0%%</td><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            naarEuro($incl),
            naarEuro($excl),
            naarEuro($btw)
        );
    }
    ?>
</table>

<h1>Betaalde BTW</h1>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Tarief</th>
            <th>Incl.</th>
            <th>Excl.</th>
            <th>BTW</th>
        </tr>
    </thead>


    <?php
    while (list($tarief, $incl, $excl, $btw) = mysql_fetch_row($btwaf))
    {
        printf('<tr><td>%s%%</td><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            $tarief,
            naarEuro($incl),
            naarEuro($excl),
            naarEuro($btw)
        );
    }
    while (list($incl, $excl, $btw) = mysql_fetch_row($btwafttlm0))
    {
        printf('<tr><td>Totaal met 0%%</td class="text-right"><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            naarEuro($incl),
            naarEuro($excl),
            naarEuro($btw)
        );
    }
    while (list($incl, $excl, $btw) = mysql_fetch_row($btwafttlz0))
    {
        printf('<tr><td>Totaal zonder 0%%</td><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            naarEuro($incl),
            naarEuro($excl),
            naarEuro($btw)
        );
    }
    ?>
</table>
<?php
$pagina->toonPostPagina();
