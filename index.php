<?php
require_once('functies.php');
?>
<html>
<head>
<link href="stijl.css" rel="stylesheet" type="text/css" />
</head>
<body class="menu">
Rekeningen:<br>
<ul>
<?php
$rekeningen=mysql_query("SELECT afkorting, omschrijving FROM rekeningen;");
while (list($afkorting, $omschrijving) = mysql_fetch_row($rekeningen))
{
	echo '<li>'.$omschrijving.': ';

/*	$minjaar=eenregel("SELECT min(DATE_FORMAT(datum, '%Y')) FROM mutaties WHERE rekening='$afkorting' AND datum<>0000-00-00 ;");
	$maxjaar=eenregel("SELECT max(DATE_FORMAT(datum, '%Y')) FROM mutaties WHERE rekening='$afkorting' AND datum<>0000-00-00 ;");
	if ($minjaar && $maxjaar)
	{
		for ($teller=$maxjaar; $teller>=$minjaar; $teller--)
		{
			echo '<a href="rekeningbijwerken.php?afkorting=' . $afkorting . '&toonjaar='.$teller.'">' . $teller . '&nbsp;&nbsp;&nbsp;</a>';
		}*/

	$data=mysql_query("SELECT distinct DATE_FORMAT(datum, '%Y') FROM mutaties WHERE rekening='$afkorting' ORDER BY 1 DESC;");
	while (list($jaar) = mysql_fetch_row($data))
	{
		echo '<a href="rekeningbijwerken.php?afkorting=' . $afkorting . '&toonjaar='.$jaar.'">' . $jaar . '&nbsp;&nbsp;&nbsp;</a>';
	}
	echo '</li>';
}
?>
</ul>
<a href="grootboek.php">Grootboek</a><br />
<a href="contributieoverzicht.php">Contributieoverzicht</a><br />
Saldioverzichten:<br>
<ul>
<?php
$rekeningen=mysql_query("SELECT afkorting, omschrijving FROM rekeningen;");
while (list($afkorting, $omschrijving) = mysql_fetch_row($rekeningen))
{
	echo '<li><a href="saldioverzicht.php?afkorting=' . $afkorting . '">' . $omschrijving . '</a></li>';
}
?>
<li><a href="saldioverzicht.php">Alle rekeningen</a></li>
</ul>
Postenoverzichten:<br>
<ul>
<li><a href="postoverzichtar.php">Alle rekeningen</a></li>
<?php
$rekeningen=mysql_query("SELECT afkorting, omschrijving FROM rekeningen;");
while (list($afkorting, $omschrijving) = mysql_fetch_row($rekeningen))
{
	echo '<li><a href="postoverzicht.php?afkorting=' . $afkorting . '">' . $omschrijving . '</a></li>';
}
?>
</ul>
Speciale overzichten:<br>
<ul>
<?php
/*
$overzichten=mysql_query("SELECT id, omschrijving FROM overzichten ORDER BY id ASC;");
while (list($oid, $omschrijving) = mysql_fetch_row($overzichten))
{
	echo '<li><a href="overzicht.php?oid=' . $oid . '">' . $omschrijving . '</a></li>';
}
*/
echo '<li><a href="resultatenrekening.php">Resultatenrekening</a></li>';
echo '<li><a href="inkomstenuitgaven.php">Staat van inkomsten en uitgaven</a></li>';
/*disconnect();*/
?>
</ul>
<a href="instellingen.php">Instellingen en standaardwaarden</a><br />
<a href="codes.php">Codes</a><br />
<a href="btw.php">BTW-overzicht</a><br />
</body>
</html>
