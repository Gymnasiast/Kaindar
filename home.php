<?php

namespace Kaindar;
require_once('functies.php');


$pagina = new Pagina('Hoofdmenu');
$pagina->toonPrepagina();

?>
    Rekeningen:<br>
    <ul>
        <?php
        $rekeningen = mysql_query("SELECT afkorting, omschrijving FROM rekeningen;");
        while (list($afkorting, $omschrijving) = mysql_fetch_row($rekeningen))
        {
            echo '<li>' . $omschrijving . ': ';

            $data = mysql_query("SELECT distinct DATE_FORMAT(datum, '%Y') FROM mutaties WHERE rekening='$afkorting' ORDER BY 1 DESC;");
            while (list($jaar) = mysql_fetch_row($data))
            {
                echo '<a href="rekeningbijwerken?afkorting=' . $afkorting . '&toonjaar=' . $jaar . '">' . $jaar . '&nbsp;&nbsp;&nbsp;</a>';
            }
            echo '</li>';
        }
        ?>
    </ul>

    Maandsaldi en cashflow:<br>
    <ul>
        <?php
        foreach (geefAlleRekeningen() as $rekening)
        {
            echo '<li><a href="saldioverzicht?afkorting=' . $rekening['afkorting'] . '">' . $rekening['omschrijving'] . '</a></li>';
        }
        ?>
        <li><a href="saldioverzicht">Alle rekeningen</a></li>
    </ul>
    Opgetelde posten:<br>
    <ul>
        <?php
        foreach (geefAlleRekeningen() as $rekening)
        {
            echo '<li><a href="postoverzicht?afkorting=' . $rekening['afkorting'] . '">' . $rekening['omschrijving'] . '</a></li>';
        }
        ?>
        <li><a href="postoverzicht">Alle rekeningen</a></li>
    </ul>
    Speciale overzichten:<br>
    <ul>
        <li><a href="btw">BTW-overzicht</a></li>
        <li><a href="contributieoverzicht">Contributieoverzicht</a></li>
        <li><a href="grootboek">Grootboek</a></li>
        <li><a href="resultatenrekening">Resultatenrekening</a></li>
        <li><a href="inkomstenuitgaven">Staat van inkomsten en uitgaven</a></li>
    </ul>
    <a href="instellingen">Instellingen en standaardwaarden</a><br/>
    <a href="codes">Codes</a><br/>

    <?php
$pagina->toonPostPagina();