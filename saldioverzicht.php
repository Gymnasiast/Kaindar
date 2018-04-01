<?php

namespace Kaindar;

use Cyndaron\DBConnection;

$afkorting = $_GET['afkorting'] ?? '';
if (!$afkorting)
{
    $rekeningstring = '';
}
else
{
    $rekeningstring = "WHERE rekening='$afkorting' ";
}

$overzichten = DBConnection::doQueryAndReturnFetchable("SELECT SUM(bij)-SUM(af) AS cashflow,DATE_FORMAT(datum,'%Y') AS jaar,DATE_FORMAT(datum,'%m') AS maand FROM mutaties $rekeningstring GROUP BY jaar,maand ORDER BY jaar DESC, maand DESC;");

$laatstejaar = 0;
$saldo = Util::geefHuidigSaldo($afkorting);

$pagina = new Pagina('Overzicht maandsaldi');
$pagina->toonPrepagina();
?>
    <table class="table table-bordered table-striped">
        <?php
        while ($overzicht = $overzichten->fetch())
        {
            if ($laatstejaar != $overzicht['jaar'])
            {
                $laatstejaar = $overzicht['jaar'];
                echo '<tr><td colspan="100%"><h1>' . $overzicht['jaar'] . '</h1></td></tr>';
            }

            echo '<tr><td>' . Util::geefMaandnaam($overzicht['maand']) . '</td><td>' . Util::naarEuro($overzicht['cashflow']) . '</td><td>' . Util::naarEuro($saldo) . '</td></tr>';
            $saldo = $saldo - $overzicht['cashflow'];
        }
        ?>
    </table>
    <?php
$pagina->toonPostPagina();