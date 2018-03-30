<?php
require 'config.php';
require_once('functies.kaindar.php');
$pdo = new PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8',$dbUser,$dbPass);

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

function mysql_num_rows(PDOStatement $mysql_query_result)
{
    return $mysql_query_result->rowCount();
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
