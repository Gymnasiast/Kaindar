<?php
require_once('functies.php');
$maxjaar=eenregel("SELECT MAX(DATE_FORMAT(datum, '%Y')) FROM mutaties ;");
$minjaar=eenregel("SELECT MIN(DATE_FORMAT(datum, '%Y')) FROM mutaties ;");
$jaar = $_POST['jaar'] ?? $jaar=eenregel("SELECT waarde FROM instellingen WHERE instelling=\"jaar\" ;");

$btwbij=mysql_query("SELECT DISTINCT btw, SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE bij<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$jaar GROUP BY btw ;");
$btwaf=mysql_query("SELECT DISTINCT btw, SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE af<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$jaar GROUP BY btw ;");
$btwbijttlz0=mysql_query("SELECT SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE btw<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$jaar ;");
$btwafttlz0=mysql_query("SELECT SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE btw<>0 AND code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$jaar ;");
$btwbijttlm0=mysql_query("SELECT SUM(bij), SUM(ROUND((bij*(100/(100+btw))), 2)), SUM(ROUND((bij*(btw/(100+btw))), 2)) FROM mutaties WHERE code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$jaar ;");
$btwafttlm0=mysql_query("SELECT SUM(af), SUM(ROUND((af*(100/(100+btw))), 2)), SUM(ROUND((af*(btw/(100+btw))), 2)) FROM mutaties WHERE code IN (SELECT code FROM codes WHERE iskruispost=0 AND isdc=0 ) AND DATE_FORMAT(datum, '%Y')=$jaar ;");
?>

<html>
<head>
<link href="stijl.css" rel="stylesheet" type="text/css" />
</head>
<body>
<p>
<a href="/">Terug naar het hoofdmenu</a>
</p>

<form method="post" action="btw.php">
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
<tr><td colspan="100%"><h1>Ontvangen BTW</h1></td></tr>
<tr><th>Tarief</th><th>Incl.</th><th>Excl.</th><th>BTW</th></tr>
<?php
while (list($tarief, $incl, $excl, $btw) = mysql_fetch_row($btwbij))
{
	echo "<tr><td class=\"right\">$tarief%</td>";
	$incl = number_format($incl, 2, ',', '.');
	$excl = number_format($excl, 2, ',', '.');
	$btw = number_format($btw, 2, ',', '.');
	echo "<td class=\"right\">$incl</td><td class=\"right\">$excl</td><td class=\"right\">$btw</td></tr>";
}
while (list($incl, $excl, $btw) = mysql_fetch_row($btwbijttlm0))
{ 
	$incl = number_format($incl, 2, ',', '.');
	$excl = number_format($excl, 2, ',', '.');
	$btw = number_format($btw, 2, ',', '.');
	echo "<tr><td class=\"right\">Totaal met 0%</td><td class=\"right\">$incl</td><td class=\"right\">$excl</td><td class=\"right\">$btw</td></tr>";
}
while (list($incl, $excl, $btw) = mysql_fetch_row($btwbijttlz0))
{ 
	$incl = number_format($incl, 2, ',', '.');
	$excl = number_format($excl, 2, ',', '.');
	$btw = number_format($btw, 2, ',', '.');
	echo "<tr><td class=\"right\">Totaal zonder 0%</td><td class=\"right\">$incl</td><td class=\"right\">$excl</td><td class=\"right\">$btw</td></tr>";
}
?>
<tr><td colspan="100%"><h1>Betaalde BTW</h1></td></tr>
<tr><th>Tarief</th><th>Incl.</th><th>Excl.</th><th>BTW</th></tr>
<?php
while (list($tarief, $incl, $excl, $btw) = mysql_fetch_row($btwaf))
{
	echo "<tr><td class=\"right\">$tarief%</td>";
	$incl = number_format($incl, 2, ',', '.');
	$excl = number_format($excl, 2, ',', '.');
	$btw = number_format($btw, 2, ',', '.');
	echo "<td class=\"right\">$incl</td><td class=\"right\">$excl</td><td class=\"right\">$btw</td></tr>";
}
while (list($incl, $excl, $btw) = mysql_fetch_row($btwafttlm0))
{
	$incl = number_format($incl, 2, ',', '.');
	$excl = number_format($excl, 2, ',', '.');
	$btw = number_format($btw, 2, ',', '.');
	echo "<tr><td class=\"right\">Totaal met 0%</td><td class=\"right\">$incl</td><td class=\"right\">$excl</td><td class=\"right\">$btw</td></tr>"; 
}
while (list($incl, $excl, $btw) = mysql_fetch_row($btwafttlz0))
{
	$incl = number_format($incl, 2, ',', '.');
	$excl = number_format($excl, 2, ',', '.');
	$btw = number_format($btw, 2, ',', '.');
	echo "<tr><td class=\"right\">Totaal zonder 0%</td><td class=\"right\">$incl</td><td class=\"right\">$excl</td><td class=\"right\">$btw</td></tr>"; 
}
?>
</table>
</body>
</html>
