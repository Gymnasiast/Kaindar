<?php
namespace Kaindar;

use Cyndaron\DBConnection;
use Cyndaron\Instelling;

const ORDER_BY = [
        '' => ['queryPart' => 'omschrijving ASC', 'description' => 'Omschrijving'],
        'code' => ['queryPart' => 'code ASC', 'description' => 'Code'],
    //'gebruik' => ['queryPart' => 'gebruik ASC'] ,
];

$orderBy = '';
$sql = '
    SELECT c.code, c.omschrijving,MAX(m.datum) AS laatstgebruikt, COUNT(m.code) as gebruik
    FROM codes c
    LEFT JOIN mutaties m on c.code = m.code
    GROUP BY c.code, c.omschrijving
    ORDER BY ' . ORDER_BY[$orderBy]['queryPart'] .';';

$codes = [
        'Recent gebruikt' => [],
        'Oudere codes' => [],
];
$drempel = strtotime('-2 years');

$records = DBConnection::doQueryAndReturnFetchable($sql);
while ($record = $records->fetch())
{
    if (strtotime($record['laatstgebruikt']) >= $drempel)
        $codes['Recent gebruikt'][] = $record;
    else
        $codes['Oudere codes'][] = $record;
}

$pagina = new Pagina('Grootboek');
$pagina->toonPrepagina();

if (!$_POST)
{
    ?>
    <form method="post" action="/grootboek">
        Jaar: <select name="jaar">
            <?php
            $jaren = Util::geefAlleJaren();
            var_dump($jaren);

            $grootboekjaar = Instelling::geefInstelling('jaar');

            foreach ($jaren as $jaar)
            {
                echo '<option';
                if ($grootboekjaar == $jaar)
                {
                    echo ' selected';
                }
                echo ' name="' . $jaar . '">' . $jaar . '</option>';
            }
            ?>
        </select><br/>
        <?php
        foreach ($codes as $category => $subcodes)
        {
            echo '<ul class="twocolumn nodots">';
            echo "<h2>{$category}</h2>";
            foreach ($subcodes as $subcode)
            {
                echo '<li><input type="checkbox" name="' . $subcode['code'] . '"/> ' . $subcode['omschrijving'] . '</li>';
            }
            echo '</ul>
            <input type="submit" value="Bekijken"/>
            <hr/>';
        }
        ?>
    </form>
    <?php
}
else
{
    $posten = DBConnection::doQueryAndReturnFetchable('SELECT code,omschrijving FROM codes ORDER BY omschrijving');
    echo '<h1>' . $_POST['jaar'] . '</h1>';
    echo '<a href="/grootboek">Terug naar selecteren</a><br />';
    while ($post = $posten->fetch())
    {
        if (isset($_POST[$post['code']]))
        {
            if ($_POST[$post['code']] == 'on')
            {
                echo '<h2>' . $post['omschrijving'] . '</h2>';
                $query = "SELECT id,rekening,DATE_FORMAT(datum, '%d-%m-%Y') AS datumfr, commentaar, bij, af, btw FROM mutaties WHERE DATE_FORMAT(datum, '%Y')=" . $_POST['jaar'] . " AND code=\"" . $post['code'] . '" ORDER BY datum ASC';
                $mutaties = DBConnection::doQueryAndReturnFetchable($query);
                echo '<table class="table table-bordered"><tr><th>ID</th><th>Rek.</th><th>Datum</th><th>Omschrijving</th><th>Bij</th><th>Af</th><th>&nbsp;</th></tr>';
                $bijtot = 0;
                $aftot = 0;
                while (list($id, $omschrijving, $datum, $commentaar, $bij, $af, $btw) = $mutaties->fetch())
                {
                    echo "<tr><td class=\"text-right\">$id</td><td>$omschrijving</td><td>$datum</td><td>$commentaar</td><td class=\"right\">";
                    $bijtot += $bij;
                    $aftot += $af;
                    if ($bij != 0.0)
                    {
                        echo Util::naarEuro($bij);
                    }
                    echo '</td><td class="text-right">';
                    if ($af != 0.0)
                    {
                        echo Util::naarEuro($af);
                    }
                    echo '</td><td class="text-right">';
                    if ($btw)
                    {
                        echo "$btw%";
                    }
                    echo "</td></tr>";
                }
                echo '</table><br /><br />';
                echo 'Totaal bij: ' . Util::naarEuro($bijtot) . '<br />';
                echo 'Totaal af: ' . Util::naarEuro($aftot) . '<br />';
                echo 'Totaal bij min totaal af: ' . Util::naarEuro($bijtot - $aftot) . '<br />';
            }
        }
    }
}

$pagina->toonPostPagina();
