<?php
require_once('functies.php');
if (!empty($_POST))
{
	$jaar=$_POST['jaar'];
	mysql_query("UPDATE instellingen SET waarde=\"$jaar\" WHERE instelling=\"jaar\" ;");
	$grootboekjaar=$_POST['grootboekjaar'];
	mysql_query("UPDATE instellingen SET waarde=\"$grootboekjaar\" WHERE instelling=\"grootboekjaar\" ;");
}
$maxjaar=eenregel("SELECT MAX(DATE_FORMAT(datum, '%Y')) FROM mutaties ;");
$minjaar=eenregel("SELECT MIN(DATE_FORMAT(datum, '%Y')) FROM mutaties ;");
$jaar=eenregel("SELECT waarde FROM instellingen WHERE instelling=\"jaar\" ;");
$grootboekjaar=eenregel("SELECT waarde FROM instellingen WHERE instelling=\"grootboekjaar\" ;");
?>
<html>
<head>
<link href="stijl.css" rel="stylesheet" type="text/css" />
</head>
<body>
<p>
<a href="index.php">Terug naar het hoofdmenu</a>
</p>
<form method="post" action="instellingen.php">
Standaardjaar (bij invoeren/opvragen): <select name="jaar">
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
	</select><br />
	Standaardjaar voor grootboek: <select name="grootboekjaar">
	<?php
	for ($teller=$minjaar; $teller<=$maxjaar; $teller++)
	{
		echo '<option';
		if ($teller==$grootboekjaar) echo " selected";
		echo ' value="'.$teller.'">'.$teller.'</option>';
	}
	?>
</select><br />
<input type="submit" value="Instellen" />
</form>
</body>
</html>
