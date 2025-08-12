<?php
declare(strict_types=1);

namespace Kaindar;

use Cyndaron\DBConnection;

$code = $_GET['code'] ?? '';

$record = DBConnection::doQueryAndFetchFirstRow('SELECT * FROM codes WHERE code = ?', [$code]);
if (empty($record))
{
    http_response_code(404);
    $pagina = new Pagina('Code bewerken');
    $pagina->toonPrepagina();
    echo 'Code niet gevonden!';
    $pagina->toonPostPagina();
    die();
}

if (!empty($_POST))
{
    $omschrijving = $_POST['omschrijving'];
    DBConnection::doQuery('UPDATE codes SET omschrijving = ? WHERE code = ?', [$omschrijving, $code]);
    header('Location: codes');
    exit(0);
}

$pagina = new Pagina('Code bewerken: ' . $code);
$pagina->toonPrepagina();
?>
<form method="post">
    <table>
        <tr>
            <td><label for="omschrijving">Omschrijving:</label></td>
            <td><input type="text" name="omschrijving" id="omschrijving" class="form-control" value="<?=$record['omschrijving']?>"/></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" class="btn btn-primary" value="Opslaan"/></td>
        </tr>
    </table>
</form>
<?php
$pagina->toonPostPagina();