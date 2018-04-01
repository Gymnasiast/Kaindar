<?php
namespace Kaindar;

use Cyndaron\DBConnection;
use Cyndaron\Instelling;

if (!empty($_POST))
{
    $jaar = $_POST['jaar'];
    DBConnection::doQuery('UPDATE instellingen SET `waarde`=? WHERE `instelling`="jaar"', [$jaar]);
}

$jaren = Util::geefAlleJaren();
$jaar = Instelling::geefInstelling('jaar');

$pagina = new Pagina('Instellingen');
$pagina->toonPrepagina();
?>
<form method="post" action="/instellingen">
    Standaardjaar (bij invoeren/opvragen): <select name="jaar">
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
    </select><br/>
    <input type="submit" value="Instellen" class="btn btn-primary"/>
</form>
<?php
$pagina->toonPostPagina();