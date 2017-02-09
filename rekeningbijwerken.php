<?php
require_once('functies.php');
require_once('functies.kaindar.php');

connect();
$afkorting=$_GET['afkorting'];
$toonjaar=$_GET['toonjaar'];
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
	if (!$bij) $bij = 0;
	if (!$af) $af = 0;
	if (!$btw) $btw = 0;
	mysql_query("INSERT INTO mutaties VALUES (NULL, \"$afkorting\", \"$code\", '$datum', \"$commentaar\", \"$bij\", \"$af\", \"$btw\");");
}
?>
<html>
<head>
<link href="stijl.css" rel="stylesheet" type="text/css" />
</head>
<body>
<p>
<a href="index.php">Terug naar het hoofdmenu</a>
</p>
<?php
$jaar=eenregel("SELECT waarde FROM instellingen WHERE instelling=\"jaar\";");
echo '
<form method="post" action="rekeningbijwerken.php?afkorting=' . $afkorting . ($toonjaar>0 ? '&amp;toonjaar='.$toonjaar : '') . '">';
?>
<table class="geenlijnen">
<tr><td class="geenlijnen">Code:</td><td class="geenlijnen"><input type="text" maxlength="10" size="10" name="code" /></td></tr>
<tr><td class="geenlijnen">Dag:</td><td class="geenlijnen"><input type="text" maxlength="2" size="2" name="dag" /></td></tr>
<tr><td class="geenlijnen">Maand:</td><td class="geenlijnen"><input type="text" maxlength="2" size="2" name="maand" /></td></tr>
<tr><td class="geenlijnen">Jaar:</td><td class="geenlijnen"><input type="text" maxlength="4" size="4" name="jaar" value="<?php echo $jaar ?>" /></td></tr>
<tr><td class="geenlijnen">Commentaar:</td><td class="geenlijnen"><input type="text" maxlength="100" name="commentaar" /></td></tr>
<tr><td class="geenlijnen">Bij:</td><td class="geenlijnen"><input type="text" name="bij" /></td></tr>
<tr><td class="geenlijnen">Af:</td><td class="geenlijnen"><input type="text" name="af" /></td></tr>
<tr><td class="geenlijnen">Betaalde/ontvangen BTW (i.v.t.):</td><td class="geenlijnen"><input type="text" name="btw" /></td></tr>
<tr><td colspan="100%" class="geenlijnen"><input type="submit" value="Invoeren"></td></tr></table>
</form>
<table class="geenlijnen"><tr><td class="geenlijnen"><br />Huidig saldo: &euro; 
<?php
$saldo=eenregel("SELECT SUM(bij)-SUM(af) FROM mutaties WHERE rekening=\"$afkorting\";");
if ($toonjaar= (int)$toonjaar) {
	if ($toonjaar>0) {
		$eindjaarsaldo=eenregel("SELECT SUM(bij)-SUM(af) FROM mutaties WHERE rekening=\"$afkorting\" AND DATE_FORMAT(datum, '%Y')<=$toonjaar "); }
else { $eindjaarsaldo=""; }}
else { $eindjaarsaldo=""; }
$saldo = number_format($saldo, 2, ',', '.');
echo $saldo;
if ($eindjaarsaldo && $eindjaarsaldo!="")
{
	echo '<br />Eindsaldo '.$toonjaar.': &euro; '.number_format($eindjaarsaldo, 2, ',', '.');
}
?>
</td></tr></table>
<br />
<table>
<tr>
<th>id</th><th>Code</th><th>Omschrijving</th><th>Datum</th><th>Commentaar</th><th>Bij</th><th>Af</th><th>BTW</th><th></th><th></th>
</tr>
<?php
if ($toonjaar= (int)$toonjaar) {
	if ($toonjaar>0) {
		$jaarstring="AND DATE_FORMAT(datum, '%Y')=$toonjaar "; }
else { $jaarstring=""; }}
else { $jaarstring=""; }
$mutatiess = "SELECT id, code, \"Fout: onbekende code\", datum, DATE_FORMAT(datum, '%d-%m-%Y') AS datumnl, commentaar, bij, af, btw FROM mutaties m WHERE code NOT IN ( SELECT code FROM codes ) AND rekening=\"$afkorting\" UNION SELECT id, m.code, omschrijving, datum, DATE_FORMAT(datum, '%d-%m-%Y') AS datumnl, commentaar, bij, af, btw FROM mutaties m, codes c WHERE m.code=c.code AND rekening=\"$afkorting\" $jaarstring ORDER BY datum DESC, id DESC;";
#$mutatiess = "SELECT id, code, \"Fout: onbekende code\", datum, DATE_FORMAT(datum, '%d-%m-%Y') AS datumnl, commentaar, bij, af, btw FROM mutaties m WHERE code NOT IN ( SELECT code FROM codes ) AND rekening=\"$afkorting\" UNION SELECT id, m.code, omschrijving, datum, DATE_FORMAT(datum, '%d-%m-%Y') AS datumnl, commentaar, bij, af, btw FROM mutaties m, codes c WHERE m.code=c.code AND rekening=\"$afkorting\" $jaarstring AND m.code<>'CON' UNION SELECT id, m.code, omschrijving, datum, DATE_FORMAT(datum, '%d-%m-%Y') AS datumnl, \" \", bij, af, btw FROM mutaties m, codes c WHERE m.code=c.code AND rekening=\"$afkorting\" $jaarstring AND m.code='CON' ORDER BY datum DESC, id DESC;";
#echo $mutatiess;
$mutaties = mysql_query($mutatiess);
while (list($id, $code, $omschrijving, $datumobs, $datum, $commentaar, $bij, $af, $btw) = mysql_fetch_row($mutaties))
{
	echo "<tr><td class=\"right\">$id</td><td>$code</td><td>$omschrijving</td><td>$datum</td><td>$commentaar</td><td class=\"right\">";
	$bij = number_format($bij, 2, ',', '.');
	$af = number_format($af, 2, ',', '.');
	if ($bij!="0,00") echo "&euro; $bij";
	echo '</td><td class="right">';
	if ($af!="0,00") echo "&euro; $af";
	echo '</td><td class="right">';
	if ($btw) echo "$btw%";
	echo "</td><td><a href=\"bewerkmutatie.php?actie=bewerken&id=$id\"><img src=\"afb/bewerken.png\" alt=\"Bewerk deze mutatie\" /></a></td>
		<td><a href=\"bewerkmutatie.php?actie=verwijderen&id=$id\"><img src=\"afb/verwijderen.png\" alt=\"Verwijder deze mutatie\" /></a></td>
		</tr>";
}
disconnect();
?>
</table>
</body>
</html>
