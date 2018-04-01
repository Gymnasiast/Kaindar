<?php

namespace Kaindar;

$pagina = new Pagina('Hoofdmenu');
$pagina->toonPrepagina();

$alleRekeningen = Util::geefAlleRekeningen();

?>
    Rekeningen:<br>
    <ul>
        <?php
        foreach ($alleRekeningen as $rekening)
        {
            echo '<li>' . $rekening['omschrijving'] . ': ';

            foreach (Util::geefAlleJaren(' WHERE rekening="' . $rekening['afkorting'] . '"') as $jaar)
            {
                echo '<a href="rekeningbijwerken?afkorting=' . $rekening['afkorting'] . '&toonjaar=' . $jaar . '">' . $jaar . '&nbsp;&nbsp;&nbsp;</a>';
            }
            echo '</li>';
        }
        ?>
    </ul>

    Maandsaldi en cashflow:<br>
    <ul>
        <?php
        foreach ($alleRekeningen as $rekening)
        {
            echo '<li><a href="saldioverzicht?afkorting=' . $rekening['afkorting'] . '">' . $rekening['omschrijving'] . '</a></li>';
        }
        ?>
        <li><a href="saldioverzicht">Alle rekeningen</a></li>
    </ul>
    Opgetelde posten:<br>
    <ul>
        <?php
        foreach (Util::geefAlleRekeningen() as $rekening)
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