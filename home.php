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

        $data=mysql_query("SELECT distinct DATE_FORMAT(datum, '%Y') FROM mutaties WHERE rekening='$afkorting' ORDER BY 1 DESC;");
        while (list($jaar) = mysql_fetch_row($data))
        {
            echo '<a href="rekeningbijwerken?afkorting=' . $afkorting . '&toonjaar='.$jaar.'">' . $jaar . '&nbsp;&nbsp;&nbsp;</a>';
        }
        echo '</li>';
    }
    ?>
</ul>
<a href="grootboek">Grootboek</a><br />
<a href="contributieoverzicht">Contributieoverzicht</a><br />
Saldioverzichten:<br>
<ul>
    <?php
    $rekeningen=mysql_query("SELECT afkorting, omschrijving FROM rekeningen;");
    while (list($afkorting, $omschrijving) = mysql_fetch_row($rekeningen))
    {
        echo '<li><a href="saldioverzicht?afkorting=' . $afkorting . '">' . $omschrijving . '</a></li>';
    }
    ?>
    <li><a href="saldioverzicht">Alle rekeningen</a></li>
</ul>
Postenoverzichten:<br>
<ul>
    <li><a href="postoverzichtar">Alle rekeningen</a></li>
    <?php
    $rekeningen=mysql_query("SELECT afkorting, omschrijving FROM rekeningen;");
    while (list($afkorting, $omschrijving) = mysql_fetch_row($rekeningen))
    {
        echo '<li><a href="postoverzicht?afkorting=' . $afkorting . '">' . $omschrijving . '</a></li>';
    }
    ?>
</ul>
Speciale overzichten:<br>
<ul>
    <?php
    echo '<li><a href="resultatenrekening">Resultatenrekening</a></li>';
    echo '<li><a href="inkomstenuitgaven">Staat van inkomsten en uitgaven</a></li>';
    ?>
</ul>
<a href="instellingen">Instellingen en standaardwaarden</a><br />
<a href="codes">Codes</a><br />
<a href="btw">BTW-overzicht</a><br />
</body>
</html>
