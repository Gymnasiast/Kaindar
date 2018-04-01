<?php
namespace Kaindar;

use Cyndaron\DBConnection;
use Cyndaron\Instelling;

$huidigJaar = $_POST['jaar'] ?? Instelling::geefInstelling('jaar');

$btwbij = DBConnection::doQueryAndReturnFetchable("SELECT DISTINCT btw, SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE bij<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar GROUP BY btw ;");
$btwaf = DBConnection::doQueryAndReturnFetchable("SELECT DISTINCT btw, SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE af<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar GROUP BY btw ;");
$btwbijttlz0 = DBConnection::doQueryAndReturnFetchable("SELECT SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE btw<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar ;");
$btwafttlz0 = DBConnection::doQueryAndReturnFetchable("SELECT SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE btw<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar ;");
$btwbijttlm0 = DBConnection::doQueryAndReturnFetchable("SELECT SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar ;");
$btwafttlm0 = DBConnection::doQueryAndReturnFetchable("SELECT SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$huidigJaar ;");

$pagina = new Pagina('BTW-overzicht');
$pagina->toonPrepagina();

$jaren = Util::geefAlleJaren();
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
    while (list($tarief, $incl, $excl, $btw) = $btwbij->fetch())
    {
        printf('<tr><td>%s%%</td><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            $tarief,
            Util::naarEuro($incl),
            Util::naarEuro($excl),
            Util::naarEuro($btw)
        );
    }
    while (list($incl, $excl, $btw) = $btwbijttlm0->fetch())
    {
        printf('<tr><td>Totaal met 0%%</td class="text-right"><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            Util::naarEuro($incl),
            Util::naarEuro($excl),
            Util::naarEuro($btw)
        );
    }
    while (list($incl, $excl, $btw) = $btwbijttlz0->fetch())
    {
        printf('<tr><td>Totaal zonder 0%%</td><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            Util::naarEuro($incl),
            Util::naarEuro($excl),
            Util::naarEuro($btw)
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
    while (list($tarief, $incl, $excl, $btw) = $btwaf->fetch())
    {
        printf('<tr><td>%s%%</td><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            $tarief,
            Util::naarEuro($incl),
            Util::naarEuro($excl),
            Util::naarEuro($btw)
        );
    }
    while (list($incl, $excl, $btw) = $btwafttlm0->fetch())
    {
        printf('<tr><td>Totaal met 0%%</td class="text-right"><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            Util::naarEuro($incl),
            Util::naarEuro($excl),
            Util::naarEuro($btw)
        );
    }
    while (list($incl, $excl, $btw) = $btwafttlz0->fetch())
    {
        printf('<tr><td>Totaal zonder 0%%</td><td class="text-right">%s</td><td class="text-right">%s</td><td class="text-right">%s</td></tr>',
            Util::naarEuro($incl),
            Util::naarEuro($excl),
            Util::naarEuro($btw)
        );
    }
    ?>
</table>
<?php
$pagina->toonPostPagina();
