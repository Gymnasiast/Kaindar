<?php
namespace Kaindar;

use Cyndaron\DBConnection;
use Cyndaron\Instelling;

$posten = DBConnection::doQueryAndReturnFetchable('SELECT code,omschrijving FROM codes ORDER BY omschrijving');

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
        <ul class="twocolumn nodots">
        <?php
        while ($post = $posten->fetch())
        {
            echo '<li><input type="checkbox" name="' . $post['code'] . '"/> ' . $post['omschrijving'] . '</li>';
        }
        ?>
        </ul>
        <input type="submit" value="Bekijken"/>
    </form>
    <?php
}
else
{
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
