<?php

use Kaindar\Pagina;

require_once('functies.php');
require_once('functies.kaindar.php');

$pagina = new Pagina('Rekening bijwerken');
$pagina->toonPrepagina();

$afkorting = $_GET['afkorting'];
$toonjaar = $_GET['toonjaar'];
if (!empty($_POST))
{
    $code = $_POST['code'];
    $dag = $_POST['dag'];
    $maand = $_POST['maand'];
    $jaar = $_POST['jaar'];
    $commentaar = $_POST['commentaar'];
    $bij = $_POST['bij'];
    $af = $_POST['af'];
    $btw = $_POST['btw'];
    $datum = $jaar . '-' . $maand . '-' . $dag;
    $bij = strtr($bij, ",", ".");
    $af = strtr($af, ",", ".");
    if (!$bij)
    {
        $bij = 0;
    }
    if (!$af)
    {
        $af = 0;
    }
    if (!$btw)
    {
        $btw = 0;
    }
    mysql_query("INSERT INTO mutaties VALUES (NULL, \"$afkorting\", \"$code\", '$datum', \"$commentaar\", \"$bij\", \"$af\", \"$btw\");");
}
$jaar = eenregel("SELECT waarde FROM instellingen WHERE instelling=\"jaar\";");
echo '
<form method="post" action="rekeningbijwerken?afkorting=' . $afkorting . ($toonjaar > 0 ? '&amp;toonjaar=' . $toonjaar : '') . '">';
?>
    <table>
        <tr>
            <td><label for="code">Code:</label></td>
            <td><input type="text" maxlength="10" size="10" name="code" id="code" class="form-control"/></td>
        </tr>
        <tr>
            <td><label for="dag">Datum:</label></td>
            <td>
                <input type="text" maxlength="2" size="2" class="form-control form-control-inline" name="dag" id="dag" placeholder="dd"/>
                <input type="text" maxlength="2" size="2" class="form-control form-control-inline" name="maand" id="maand" placeholder="mm"/>
                <input type="text" maxlength="4" size="4" class="form-control form-control-inline" name="jaar" id="jaar" value="<?php echo $jaar ?>"/>
            </td>
        </tr>
        <tr>
            <td><label for="commentaar">Commentaar:</label></td>
            <td><input type="text" maxlength="100" name="commentaar" id="commentaar" class="form-control"/></td>
        </tr>
        <tr>
            <td><label for="bij">Bij:</label></td>
            <td>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">&euro;</div>
                    </div>
                    <input type="text" name="bij" id="bij" class="form-control"/>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="af">Af:</label></td>
            <td>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">&euro;</div>
                    </div>
                    <input type="text" name="af" id="af" class="form-control"/>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="btw">Betaalde/ontvangen BTW (i.v.t.):</label></td>
            <td>
                <div class="input-group">
                    <input type="text" name="btw" id="btw" class="form-control"/>
                    <div class="input-group-append">
                        <div class="input-group-text">%</div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="100%"><input type="submit" class="btn btn-primary" value="Invoeren"></td>
        </tr>
    </table>
    </form>
    <table>
        <tr>
            <td><br/>Huidig saldo: &euro;
                <?php
                $saldo = eenregel("SELECT SUM(bij)-SUM(af) FROM mutaties WHERE rekening=\"$afkorting\";");
                if ($toonjaar = (int)$toonjaar)
                {
                    if ($toonjaar > 0)
                    {
                        $eindjaarsaldo = eenregel("SELECT SUM(bij)-SUM(af) FROM mutaties WHERE rekening=\"$afkorting\" AND DATE_FORMAT(datum, '%Y')<=$toonjaar ");
                    }
                    else
                    {
                        $eindjaarsaldo = "";
                    }
                }
                else
                {
                    $eindjaarsaldo = "";
                }
                $saldo = number_format($saldo, 2, ',', '.');
                echo $saldo;
                if ($eindjaarsaldo && $eindjaarsaldo != "")
                {
                    echo '<br />Eindsaldo ' . $toonjaar . ': &euro; ' . number_format($eindjaarsaldo, 2, ',', '.');
                }
                ?>
            </td>
        </tr>
    </table>
    <br/>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Omschrijving</th>
                <th>Datum</th>
                <th>Commentaar</th>
                <th>Bij</th>
                <th>Af</th>
                <th>BTW</th>
                <th></th>
            </tr>
        </thead>
        <tbody>

        <?php
        if ($toonjaar = (int)$toonjaar)
        {
            if ($toonjaar > 0)
            {
                $jaarstring = "AND DATE_FORMAT(datum, '%Y')=$toonjaar ";
            }
            else
            {
                $jaarstring = "";
            }
        }
        else
        {
            $jaarstring = "";
        }
        $mutatiess = "SELECT m.id, code, \"Fout: onbekende code\", datum, DATE_FORMAT(datum, '%d-%m-%Y') AS datumnl, commentaar, bij, af, btw FROM mutaties m WHERE code NOT IN ( SELECT code FROM codes ) AND rekening=\"$afkorting\" UNION SELECT m.id, m.code, omschrijving, datum, DATE_FORMAT(datum, '%d-%m-%Y') AS datumnl, commentaar, bij, af, btw FROM mutaties m, codes c WHERE m.code=c.code AND rekening=\"$afkorting\" $jaarstring ORDER BY datum DESC, id DESC;";
        $mutaties = mysql_query($mutatiess);
        while (list($id, $code, $omschrijving, $datumobs, $datum, $commentaar, $bij, $af, $btw) = mysql_fetch_row($mutaties))
        {
            echo "<tr><td class=\"right\">$id</td><td>$code</td><td>$omschrijving</td><td>$datum</td><td>$commentaar</td><td class=\"right\">";
            if ($bij != 0.0)
            {
                echo naarEuro($bij);
            }
            echo '</td><td class="right">';
            if ($af != 0.0)
            {
                echo naarEuro($af);
            }
            echo '</td><td class="right">';
            if ($btw)
            {
                echo "$btw%";
            }
            echo '</td>';
            ?>
            <td>
                <div class="btn-group btn-group-sm">
                    <a class="btn btn-info" href="/bewerkmutatie?actie=bewerken&id=<?=$id;?>"><span class="fa fa-pencil"></span></a>
                    <a class="btn btn-danger" href="/bewerkmutatie?actie=verwijderen&id=<?=$id;?>"><span class="fa fa-trash"></span></a>
                </div>
            </td>
		</tr>
        <?php
        }
        ?>
        </tbody>

    </table>
<?php
$pagina->toonPostPagina();
