<?php
require_once('functies.php');
connect();
$minjaar=eenregel("SELECT MIN(DATE_FORMAT(datum, '%Y')) FROM mutaties;");
$maxjaar=eenregel("SELECT MAX(DATE_FORMAT(datum, '%Y')) FROM mutaties;");
$posten=mysql_query('SELECT code,omschrijving FROM codes ORDER BY omschrijving');
echo '<html><head><title>Grootboek</title><style type="text/css">
td { 
border: 1px #000000;
}
</style>
<link href="stijl.css" rel="stylesheet" type="text/css" />
</head><body>
	';
	echo '<a href="index.php">Terug naar hoofdmenu</a><br />';
if (!$_POST)
{
	?>
	<form method="post" action="grootboek.php">
	Jaar: <select name="jaar">
	<?php
	$grootboekjaar=eenregel("SELECT waarde FROM instellingen WHERE instelling=\"grootboekjaar\" ;");
	for ($teller=$minjaar; $teller<=$maxjaar; $teller++)
	{
		echo '<option';
		if ($grootboekjaar==$teller)
		{
			echo ' selected';
		}		
		echo ' name="'.$teller.'">'.$teller.'</option>';
	}
	?>
	</select><br />
	<?php
	$teller=1;
	while($post = mysql_fetch_assoc($posten))
	{
		echo '<input type="checkbox" name="'.$post['code'].'"/> '.$post['omschrijving'].'<br />';
	}
	?>
	<input type="submit" value="Bekijken" />
	</form>
<?php
}
else
{
	echo '<h1>'.$_POST['jaar'].'</h1>';
	echo '<a href="grootboek.php">Terug naar selecteren</a><br />';
	while($post = mysql_fetch_assoc($posten))
	{

		if (isset($_POST[$post['code']]))
		{
			if ($_POST[$post['code']]=='on')
			{
				echo '<h2>'.$post['omschrijving'].'</h2>';
				$query="SELECT id,rekening,DATE_FORMAT(datum, '%d-%m-%Y') AS datumfr, commentaar, bij, af, btw FROM mutaties WHERE DATE_FORMAT(datum, '%Y')=".$_POST['jaar']." AND code=\"".$post['code'].'" ORDER BY datum ASC';
				#echo $query;
				$mutaties=mysql_query($query);
				echo '<table><tr><th>ID</th><th>Rek.</th><th>Datum</th><th>Omschrijving</th><th>Bij</th><th>Af</th><th>&nbsp;</th></tr>';
				$bijtot=0;
				$aftot=0;
				while (list($id, $omschrijving, $datum, $commentaar, $bij, $af, $btw) = mysql_fetch_row($mutaties))
				{
					echo "<tr><td class=\"right\">$id</td><td>$omschrijving</td><td>$datum</td><td>$commentaar</td><td class=\"right\">";
					$bijtot+=$bij;
					$aftot+=$af;
					$bij = number_format($bij, 2, ',', '.');
					$af = number_format($af, 2, ',', '.');
					if ($bij!="0,00") echo "&euro; $bij";
					echo '</td><td class="right">';
					if ($af!="0,00") echo "&euro; $af";
					echo '</td><td class="right">';
					if ($btw) echo "$btw%";
					echo "</td></tr>";
				}
				echo '</table><br /><br />';
				echo 'Totaal bij: '.number_format($bijtot, 2, ',', '.').'<br />';
				echo 'Totaal af: '.number_format($aftot, 2, ',', '.').'<br />';
				echo 'Totaal bij min totaal af: '.number_format($bijtot-$aftot, 2, ',', '.').'<br />';
			}
		}	
	}
}
echo '</body></html>';
?>
