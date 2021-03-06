<?php
namespace Kaindar;

use Cyndaron\DBConnection;

$id=$_GET['id'];
$actie=$_GET['actie'];

$pagina = new Pagina('Mutatie bijwerken');
$pagina->toonPrepagina();

if (!empty($_POST))
{
	if ($actie=="bewerken")
	{
		$code = $_POST['code'];
		$rekening = $_POST['rekening'];
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
		if (!$bij) $bij = 0;
		if (!$af) $af = 0;
		if (!$btw) $btw = 0;
		DBConnection::doQueryAndReturnFetchable("UPDATE mutaties SET code=\"$code\", rekening=\"$rekening\", commentaar=\"$commentaar\", datum=\"$datum\", bij=$bij, af=$af, btw=$btw WHERE id=$id ;");
		echo 'Mutatie bewerkt. <br /><a class="btn btn-primary" href="rekeningbijwerken?afkorting=' . $rekening . '&toonjaar='.$jaar.'">Terug naar het invoerscherm</a>';
	}
	elseif ($actie=='verwijderen')
	{
		$rekening = DBConnection::doQueryAndFetchOne('SELECT rekening FROM mutaties WHERE id=?', [$id]);
		$jaar=DBConnection::doQueryAndFetchOne('SELECT DATE_FORMAT(datum, \'%Y\') FROM mutaties WHERE id=?', [$id]);
		DBConnection::doQuery('DELETE FROM mutaties WHERE id=?', [$id]);
		echo 'Mutatie verwijderd. <br /><a class="btn btn-primary" href="rekeningbijwerken?afkorting=' . $rekening . '&toonjaar='.$jaar.'">Terug naar het invoerscherm</a>';
	}
}
else
{
	if ($actie=="bewerken")
	{
		$mutatie=DBConnection::doQueryAndReturnFetchable("SELECT code, rekening, DATE_FORMAT(datum, '%d'), DATE_FORMAT(datum, '%m'), DATE_FORMAT(datum, '%Y'), commentaar, bij, af, btw FROM mutaties WHERE id=$id");
		while (list($code, $rekening, $dag, $maand, $jaar, $commentaar, $bij, $af, $btw) = $mutatie->fetch())
		{
			echo '
			<form method="post" action="bewerkmutatie?actie=bewerken&id='.$id.'">
			<table class="geenlijnen">
			<tr><td class="geenlijnen">Code:</td><td class="geenlijnen"><input type="text" size="10" name="code" value="'.$code.'" /></td></tr>
			<tr><td class="geenlijnen">Rekeningcode:</td><td class="geenlijnen">
			<select name="rekening">';
			$rekeningen=DBConnection::doQueryAndReturnFetchable("SELECT afkorting, omschrijving FROM rekeningen ;");
			while (list($rcode, $romschrijving) = $rekeningen->fetch())
			{
				echo "<option value=\"$rcode\"";
				if ($rcode==$rekening) echo " selected";
				echo ">$romschrijving</option>";
			}
			echo '</select></td></tr> 
			<tr><td class="geenlijnen">Dag:</td><td class="geenlijnen"><input type="text" maxlength="2" size="2" name="dag" value="'.$dag.'" /></td></tr>
			<tr><td class="geenlijnen">Maand:</td><td class="geenlijnen"><input type="text" maxlength="2" size="2" name="maand" value="'.$maand.'" /></td></tr>
			<tr><td class="geenlijnen">Jaar:</td><td class="geenlijnen"><input type="text" maxlength="4" size="4" name="jaar" value="'.$jaar.'" /></td></tr>
			<tr><td class="geenlijnen">Commentaar:</td><td class="geenlijnen"><input type="text" maxlength="100" name="commentaar" value="'.$commentaar.'" /></td></tr>
			<tr><td class="geenlijnen">Bij:</td><td class="geenlijnen"><input type="text" name="bij" value="'.$bij.'" /></td></tr>
			<tr><td class="geenlijnen">Af:</td><td class="geenlijnen"><input type="text" name="af" value="'.$af.'" /></td></tr>
			<tr><td class="geenlijnen">Betaalde/ontvangen BTW (i.v.t.):</td><td class="geenlijnen"><input type="text" name="btw" value="'.$btw.'" /></td></tr>
			<tr><td colspan="100%" class="geenlijnen"><input type="submit" value="Bewerken"></td></tr><table>
			</form>';
		}
	}
	elseif ($actie=='verwijderen')
	{	
		?>
		<form method="post" action="bewerkmutatie?actie=verwijderen&amp;id=<?php echo $id; ?>">
			<p>
			<input name="verwijderen" type="hidden" value="verwijderen">
			<input type="submit" class="btn btn-danger" value="Verwijderen bevestigen">
			</p>
		</form>
		<?php
	}
}

$pagina->toonPostPagina();