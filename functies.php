<?php
require_once('functies.kaindar.php');
$pdo = new PDO('mysql:host=localhost;dbname=-;charset=utf8','-','-');

function connect()
{
	return;
	/*// Alleen doorgaan als het verbinden lukt.
	if (mysql_connect("localhost", "-", "-") AND mysql_select_db("administratie"))
	{
	}
	else
	{
		die("Kan niet verbinden met database");
	}*/
}
function disconnect()
{
	// Database netjes afsluiten
	//mysql_close();
}

function mysql_query($query)
{
	global $pdo;
	$prep = $pdo->prepare($query);
	$prep->execute([]);
	return $prep;

}
function mysql_fetch_row($mysql_query_result)
{
	return $mysql_query_result->fetch();
}

function mysql_fetch_assoc($mysql_query_result)
{
	return $mysql_query_result->fetch();
}

function eenregel($query)
{
	// Functie om een query met een cel als resultaat alvast door een while-statement te halen voor de
	// overzichtelijkheid
	$resultaatr=mysql_query($query);
	while(list($resultaat) = mysql_fetch_row($resultaatr))
	{
		return $resultaat;
	}
}
