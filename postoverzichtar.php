<?php
require_once('functies.php');
$maxjaar=eenregel("SELECT MAX(DATE_FORMAT(datum, '%Y')) FROM mutaties ;");
$minjaar=eenregel("SELECT MIN(DATE_FORMAT(datum, '%Y')) FROM mutaties ;");
$jaar=$_POST['jaar'];
if (!$jaar)
{
	$jaar=eenregel("SELECT waarde FROM instellingen WHERE instelling=\"jaar\" ;");
}
$posten=mysql_query('SELECT DISTINCT m.code, omschrijving, SUM(bij), SUM(af), SUM(ROUND((bij*(btw/(100+btw))), 2)) AS "Ontvangen BTW", SUM(ROUND((af*(btw/(100+btw))), 2)) AS "Betaalde BTW" FROM mutaties m, codes c WHERE m.code=c.code AND DATE_FORMAT(datum, \'%Y\')=' . $jaar . ' GROUP BY m.code ORDER BY omschrijving ASC;');
?>
<html>
<head>
<link href="stijl.css" rel="stylesheet" type="text/css" />
</head>
<body>
<p>
<a href="index.php">Terug naar het hoofdmenu</a>
</p>
Opgetelde posten van alle rekeningen van het jaar <?php echo $jaar; ?>.
<form method="post" action="postoverzichtar.php">
<select name="jaar">
<?php
$teller=$maxjaar;
while ($teller>=$minjaar)
{
	echo "<option";
	if ($teller==$jaar) echo " selected";
	echo " value=\"$teller\">$teller</option>";
	$teller--;
}
?>
</select>
<input type="submit" value="Weergeven" />
</form>
<table>
<tr><th>Post</th><th>Totaal bij</th><th>Totaal af</th><th>Saldo bij-af</th><th>Ontv. BTW</th><th>Bet. BTW</th></tr>
<?php
while (list($code, $omschrijving, $bij, $af, $ontvbtw, $betbtw) = mysql_fetch_row($posten))
{
	$bijminaf=number_format($bij-$af, 2, ',', '.');	
	$bij=number_format($bij, 2, ',', '.');
	$af=number_format($af, 2, ',', '.');
	$ontvbtw=number_format($ontvbtw, 2, ',', '.');
	$betbtw=number_format($betbtw, 2, ',', '.');
	echo "<tr><td>$omschrijving</td><td class=\"right\">&euro; $bij</td><td class=\"right\">&euro; $af</td><td class=\"right\">&euro; $bijminaf</td><td class=\"right\">&euro; $ontvbtw</td><td class=\"right\">&euro; $betbtw</td></tr>";
}
?>
</table>
</body>
</html>
