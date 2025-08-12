<?php
namespace Kaindar;

use Cyndaron\DBConnection;
use function array_key_exists;

const ORDER_BY = [
    '' => ['queryPart' => 'omschrijving ASC', 'description' => 'Omschrijving'],
    'code' => ['queryPart' => 'code ASC', 'description' => 'Code'],
    //'gebruik' => ['queryPart' => 'gebruik ASC'] ,
];

$orderBy = $_GET['sort'] ?? '';
if (!array_key_exists($orderBy, ORDER_BY))
{
    $orderBy = '';
}

if (!empty($_POST))
{
    $action = $_POST['action'] ?? '';
    if ($action === 'add')
    {
        $code = $_POST['code'];
        $omschrijving = $_POST['omschrijving'];
        DBConnection::doQuery('INSERT INTO codes VALUES (?, ?, 0, 0)', [$code, $omschrijving]);
    }
    elseif ($action === 'delete')
    {
        $code = $_POST['code'];
        DBConnection::doQuery('DELETE FROM codes WHERE code = ?', [$code]);
    }
}

$sql = '
    SELECT c.code, c.omschrijving,MAX(m.datum) AS laatstgebruikt, COUNT(m.code) as gebruik
    FROM codes c
    LEFT JOIN mutaties m on c.code = m.code
    GROUP BY c.code, c.omschrijving
    ORDER BY ' . ORDER_BY[$orderBy]['queryPart'] .';';

$codes = [
    'Recent gebruikt' => [],
    'Oudere codes' => [],
];
$drempel = strtotime('-2 years');

$records = DBConnection::doQueryAndReturnFetchable($sql);
while ($record = $records->fetch())
{
    if (strtotime($record['laatstgebruikt']) >= $drempel)
        $codes['Recent gebruikt'][] = $record;
    else
        $codes['Oudere codes'][] = $record;
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
            <td><input type="text" maxlength="15" size="15" id="code" name="code" class="form-control"/></td>
        </tr>
        <tr>
            <td><label for="omschrijving">Omschrijving:</label></td>
            <td><input type="text" maxlength="100" id="omschrijving" name="omschrijving" class="form-control"/></td>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="action" value="add"/>
                <input type="submit" class="btn btn-primary" value="Toevoegen"/>
            </td>
        </tr>
    </table>
</form>

<h2>Bestaande codes</h2>

<form method="get">
    Sorteren op:
    <?php foreach (ORDER_BY as $key => $desc): ?>
        <input id="sort-<?=$key?>" type="radio" name="sort" value="<?=$key?>" <?php if ($orderBy === $key): ?>checked<?php endif;?>/>
        <label for="sort-<?=$key?>"><?=$desc['description']?></label>
    <?php endforeach; ?>
    <input type="submit" value="Toepassen" class="btn btn-outline-primary"/>
</form>


<?php foreach ($codes as $kop => $records): ?>
    <h3><?=$kop?></h3>
    <table class="table table-bordered table-striped">
        <tr>
            <th>Code</th>
            <th>Omschrijving</th>
            <th>Laatst gebruikt</th>
            <th>Gebruikt</th>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($records as list($code, $omschrijving, $laatstGebruikt, $gebruik)): ?>
            <tr>
                <td><?=$code?></td>
                <td><?=$omschrijving?></td>
                <td><?=$laatstGebruikt?></td>
                <td><?=$gebruik?>×</td>
                <td><a href="bewerkcode?code=<?=$code?>" class="btn btn-outline-primary">Bewerken</a></td>
                <td>
                    <?php if ($gebruik == 0): ?>
                        <form method="post">
                            <input type="hidden" name="code" value="<?=$code?>"/>
                            <input type="hidden" name="action" value="delete"/>
                            <input type="submit" value="Verwijderen" class="btn btn-outline-primary"/>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php endforeach;

$pagina->toonPostPagina();

