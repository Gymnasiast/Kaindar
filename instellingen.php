<?php
namespace Kaindar;

require_once('functies.php');
if (!empty($_POST))
{
    $jaar = $_POST['jaar'];
    mysql_query("UPDATE instellingen SET waarde=\"$jaar\" WHERE instelling=\"jaar\" ;");
}

$jaren = geefAlleJaren();
$jaar = eenregel("SELECT waarde FROM instellingen WHERE instelling=\"jaar\" ;");

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