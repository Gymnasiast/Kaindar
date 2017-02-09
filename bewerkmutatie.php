<?php
require_once('functies.php');
connect();
$id=$_GET['id'];
$actie=$_GET['actie'];
?>
<html>
<head>
<title>Mutatie bewerken/verwijderen</title>
<link href="stijl.css" rel="stylesheet" type="text/css">
</head>
<body>
<p>
<a href="index.php">Terug naar het hoofdmenu</a>
</p>
<?php
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
		mysql_query("UPDATE mutaties SET code=\"$code\", rekening=\"$rekening\", commentaar=\"$commentaar\", datum=\"$datum\", bij=$bij, af=$af, btw=$btw WHERE id=$id ;");
		echo 'Mutatie bewerkt. <a href="rekeningbijwerken.php?afkorting=' . $rekening . '&toonjaar='.$jaar.'">Terug naar het invoerscherm.';
	}
	elseif ($actie=='verwijderen')
	{
		$rekening=eenregel("SELECT rekening FROM mutaties WHERE id=$id ;");
		$jaar=eenregel("SELECT DATE_FORMAT(datum, '%Y') FROM mutaties WHERE id=$id ;");
		mysql_query("DELETE FROM mutaties WHERE id=$id ;");
		echo 'Mutatie verwijderd. <a href="rekeningbijwerken.php?afkorting=' . $rekening . '&toonjaar='.$jaar.'">Terug naar het invoerscherm.';		
	}
}
else
{
	if ($actie=="bewerken")
	{
		$mutatie=mysql_query("SELECT code, rekening, DATE_FORMAT(datum, '%d'), DATE_FORMAT(datum, '%m'), DATE_FORMAT(datum, '%Y'), commentaar, bij, af, btw FROM mutaties WHERE id=$id");
		while (list($code, $rekening, $dag, $maand, $jaar, $commentaar, $bij, $af, $btw) = mysql_fetch_row($mutatie))
		{
			echo '
			<form method="post" action="bewerkmutatie.php?actie=bewerken&id='.$id.'">
			<table class="geenlijnen">
			<tr><td class="geenlijnen">Code:</td><td class="geenlijnen"><input type="text" size="10" name="code" value="'.$code.'" /></td></tr>
			<tr><td class="geenlijnen">Rekeningcode:</td><td class="geenlijnen">
			<select name="rekening">';
			$rekeningen=mysql_query("SELECT afkorting, omschrijving FROM rekeningen ;");
			while (list($rcode, $romschrijving) = mysql_fetch_row($rekeningen))
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
		<form method="post" action="bewerkmutatie.php?actie=verwijderen&amp;id=<?php echo $id; ?>">
			<p>
			<input name="verwijderen" type="hidden" value="verwijderen">
			<input type="submit" value="Verwijderen bevestigen">
			</p>
		</form>
		<?php
	}
}
disconnect();
?>
</body>
</html>

