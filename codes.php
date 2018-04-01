<?php
namespace Kaindar;

require_once('functies.php');
if (!empty($_POST))
{
    $code = $_POST['code'];
    $omschrijving = $_POST['omschrijving'];
    mysql_query("INSERT INTO codes VALUES (\"$code\", \"$omschrijving\", 0, 0) ;");
}

$pagina = new Pagina('Codes');
$pagina->toonPrepagina();
?>
<form method="post" action="/codes">
    <table>
        <tr>
            <td>Nieuwe code:</td>
        </tr>
        <tr>
            <td><label for="code">Afkorting:</label></td>
            <td><input type="text" maxlength="10" size="10" id="code" name="code" class="form-control"/></td>
        </tr>
        <tr>
            <td><label for="omschrijving">Omschrijving:</label></td>
            <td><input type="text" maxlength="100" id="omschrijving" name="omschrijving" class="form-control"/></td>
        </tr>
        <tr><td><input type="submit" class="btn btn-primary" value="Invoeren"/></td></tr>
    </table>
    <table class="table table-bordered table-striped">
</form>
<tr>
    <th>Code</th>
    <th>Omschrijving</th>
</tr>
<?php
$codes = mysql_query("SELECT code, omschrijving FROM codes ORDER BY omschrijving ASC;");
while (list($code, $omschrijving) = mysql_fetch_row($codes))
{
    echo "<tr><td>$code</td><td>$omschrijving</td></tr>";
}
?>
</table>
<?php
$pagina->toonPostPagina();

