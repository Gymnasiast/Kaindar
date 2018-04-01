<?php

use Kaindar\Pagina;

require_once('functies.php');

$posten = mysql_query('SELECT code,omschrijving FROM codes ORDER BY omschrijving');

$pagina = new Pagina('Grootboek');
$pagina->toonPrepagina();

if (!$_POST)
{
    ?>
    <form method="post" action="/grootboek">
        Jaar: <select name="jaar">
            <?php
            $jaren = geefAlleJaren();
            var_dump($jaren);

            $grootboekjaar = eenregel("SELECT waarde FROM instellingen WHERE instelling=\"grootboekjaar\" ;");

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
        while ($post = mysql_fetch_assoc($posten))
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
    while ($post = mysql_fetch_assoc($posten))
    {

        if (isset($_POST[$post['code']]))
        {
            if ($_POST[$post['code']] == 'on')
            {
                echo '<h2>' . $post['omschrijving'] . '</h2>';
                $query = "SELECT id,rekening,DATE_FORMAT(datum, '%d-%m-%Y') AS datumfr, commentaar, bij, af, btw FROM mutaties WHERE DATE_FORMAT(datum, '%Y')=" . $_POST['jaar'] . " AND code=\"" . $post['code'] . '" ORDER BY datum ASC';
                $mutaties = mysql_query($query);
                echo '<table class="table table-bordered"><tr><th>ID</th><th>Rek.</th><th>Datum</th><th>Omschrijving</th><th>Bij</th><th>Af</th><th>&nbsp;</th></tr>';
                $bijtot = 0;
                $aftot = 0;
                while (list($id, $omschrijving, $datum, $commentaar, $bij, $af, $btw) = mysql_fetch_row($mutaties))
                {
                    echo "<tr><td class=\"right\">$id</td><td>$omschrijving</td><td>$datum</td><td>$commentaar</td><td class=\"right\">";
                    $bijtot += $bij;
                    $aftot += $af;
                    $bij = number_format($bij, 2, ',', '.');
                    $af = number_format($af, 2, ',', '.');
                    if ($bij != "0,00")
                    {
                        echo "&euro; $bij";
                    }
                    echo '</td><td class="right">';
                    if ($af != "0,00")
                    {
                        echo "&euro; $af";
                    }
                    echo '</td><td class="right">';
                    if ($btw)
                    {
                        echo "$btw%";
                    }
                    echo "</td></tr>";
                }
                echo '</table><br /><br />';
                echo 'Totaal bij: ' . number_format($bijtot, 2, ',', '.') . '<br />';
                echo 'Totaal af: ' . number_format($aftot, 2, ',', '.') . '<br />';
                echo 'Totaal bij min totaal af: ' . number_format($bijtot - $aftot, 2, ',', '.') . '<br />';
            }
        }
    }
}

$pagina->toonPostPagina();
