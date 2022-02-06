<?php
namespace Kaindar;

use Cyndaron\DBConnection;
use Cyndaron\Instelling;

const PERIODES = [
    0 => 'Hele jaar',
    1 => 'Jan - Mrt',
    2 => 'Apr - Jun',
    3 => 'Jul - Sep',
    4 => 'Okt - Dec',
];

$huidigJaar = (int)($_POST['jaar'] ?? Instelling::geefInstelling('jaar'));
$huidigePeriode = (int)($_POST['periode'] ?? 0);

$dateLimit = "DATE_FORMAT(datum, '%Y')=?";
$vars = [$huidigJaar];
$titel = 'BTW-overzicht ' . $huidigJaar;

if ($huidigePeriode > 0)
{
    $startmaand = (($huidigePeriode - 1) * 3) + 1;
    $eindmaand = $startmaand + 2;
    $dateLimit .= " AND DATE_FORMAT(datum, '%c') >= ? AND DATE_FORMAT(datum, '%c') <= ?";
    $vars[] = $startmaand;
    $vars[] = $eindmaand;
    $titel .= " ({$huidigePeriode}e kwartaal)";
}

$btwbij = DBConnection::doQueryAndReturnFetchable("SELECT DISTINCT btw, SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE bij<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND {$dateLimit} GROUP BY btw ;", $vars);
$btwaf = DBConnection::doQueryAndReturnFetchable("SELECT DISTINCT btw, SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE af<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND {$dateLimit} GROUP BY btw ;", $vars);
$btwbijttlz0 = DBConnection::doQueryAndReturnFetchable("SELECT SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE btw<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND {$dateLimit} ;", $vars);
$btwafttlz0 = DBConnection::doQueryAndReturnFetchable("SELECT SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE btw<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND {$dateLimit} ;", $vars);
$btwbijttlm0 = DBConnection::doQueryAndReturnFetchable("SELECT SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND {$dateLimit} ;", $vars);
$btwafttlm0 = DBConnection::doQueryAndReturnFetchable("SELECT SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND {$dateLimit} ;", $vars);

$pagina = new Pagina($titel);
$pagina->toonPrepagina();

$jaren = Util::geefAlleJaren();
?>

<form method="post" action="/btw">
    Jaar: <select name="jaar">
        <?php foreach ($jaren as $jaar): ?>
            <?php $selected = ($jaar === $huidigJaar) ? ' selected' : ''; ?>
            <option <?=$selected?> value="<?=$jaar?>"><?=$jaar?></option>
        <?php endforeach; ?>
    </select>
    Periode: <select name="periode">
        <?php foreach (PERIODES as $index => $omschrijving): ?>
            <?php $selected = ($index === $huidigePeriode) ? ' selected' : ''; ?>
            <option value="<?=$index?>" <?=$selected?>><?=$omschrijving?></option>
        <?php endforeach; ?>
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
