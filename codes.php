<?php
require_once('functies.php');
if(!empty($_POST))
{
	$code=$_POST['code'];
	$omschrijving=$_POST['omschrijving'];
	mysql_query("INSERT INTO codes VALUES (\"$code\", \"$omschrijving\", 0, 0) ;");
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
<form method="post" action="codes.php">
<table class="geenlijnen">
<tr><td class="geenlijnen">Nieuwe code:</td></tr>
<tr><td class="geenlijnen">Afkorting:</td><td class="geenlijnen"><input type="text" maxlength="10" size="10" name="code" /></td></tr>
<tr><td class="geenlijnen">Omschrijving:</td><td class="geenlijnen"><input type="text" maxlength="100" name="omschrijving" /></td></tr>
<input type="submit" value="Invoeren" />
<table>
</form>
<tr><th>Code</th><th>Omschrijving</th></tr>
<?php
$codes=mysql_query("SELECT code, omschrijving FROM codes ORDER BY omschrijving ASC;");
while (list($code, $omschrijving) = mysql_fetch_row($codes))
{
	echo "<tr><td>$code</td><td>$omschrijving</td></tr>";
}
?>
</table>
</body>
</html>
