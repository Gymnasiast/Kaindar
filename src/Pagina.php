<?php
namespace Kaindar;

use Cyndaron\Instelling;
use Cyndaron\Url;
use Cyndaron\Widget\Knop;

/*
 * Copyright © 2009-2017, Michael Steenbeek
 *
 * Permission to use, copy, modify, and/or distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */


class Pagina
{
    protected $extraMeta = "";
    protected $paginanaam = "";
    protected $titelknoppen = null;
    protected $connectie = null;
    protected $nietDelen = false;
    protected $extraScripts = [];
    protected $websitenaam = '';

    public function __construct($paginanaam)
    {
//        if ($this->connectie == null)
//        {
//            $this->connectie = DBConnection::getPDO();
//        }

        $this->paginanaam = $paginanaam;
    }

    public function maakExtraMeta($extraMeta)
    {
        $this->extraMeta = $extraMeta;
    }

    public function maaknietDelen($bool)
    {
        $this->nietDelen = (bool)$bool;
    }

    public function maakTitelknoppen($titelknoppen)
    {
        $this->titelknoppen = $titelknoppen;
    }

    public function toonPrepagina()
    {
        $this->websitenaam = 'Administratie'; // Instelling::geefInstelling('websitenaam');
        $titel = $this->paginanaam . ' - ' . $this->websitenaam;

        ?>
        <!DOCTYPE HTML>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="twitter:card" content="summary" />
            <meta name="twitter:title" content="<?=$titel;?>" />
            <meta name="twitter:description" content="Klik hier om verder te lezen..." />
            <title><?=$titel;?></title>
            <?php
            printf('<link href="/vendor/normalize.css/normalize.css?r=%s" type="text/css" rel="stylesheet" />', CyndaronInfo::ENGINE_VERSIE);
            printf('<link href="/vendor/Bootstrap/css/bootstrap.min.css?r=%s" type="text/css" rel="stylesheet" />', CyndaronInfo::ENGINE_VERSIE);
            printf('<link href="/css/kaindar.css?r=%s" type="text/css" rel="stylesheet" />', CyndaronInfo::ENGINE_VERSIE);
            if ($favicon = Instelling::geefInstelling('favicon'))
            {
                $extensie = substr(strrchr($favicon, "."), 1);
                echo '<link rel="icon" type="image/' . $extensie . '" href="' . $favicon . '">';
            }
            ?>
            <style type="text/css">
                <?php
                static::toonIndienAanwezig(Instelling::geefInstelling('achtergrondkleur'), 'body.cyndaron, .lightboxOverlay { background-color: ',";}\n");
                static::toonIndienAanwezig(Instelling::geefInstelling('menukleur'), '.menu { background-color: ',";}\n");
                static::toonIndienAanwezig(Instelling::geefInstelling('menuachtergrond'), '.menu { background-image: url(\'',"');}\n");
                static::toonIndienAanwezig(Instelling::geefInstelling('artikelkleur'), '.inhoud { background-color: ',";}\n");
                ?>
            </style>
        </head>
        <body class="kaindar" data-artikelkleur="<?=Instelling::geefInstelling('artikelkleur');?>"><?php

        echo '
        <div class="paginacontainer">
        <div class="menucontainer">';

        $this->toonMenu();

        echo '</div>';

        if ($this->isVoorPagina() && Instelling::geefInstelling('voorpagina_is_jumbo') && Instelling::geefInstelling('jumbo_inhoud'))
        {
            echo '<div class="welkom-jumbo">';
            echo Instelling::geefInstelling('jumbo_inhoud');
            echo '</div>';
        }

        echo '<div class="inhoudcontainer"><div class="inhoud">';

        $class = '';
        if ($this->isVoorPagina())
        {
            $class = 'voorpagina';
        }

        echo '<div class="paginatitel ' . $class . '"><h1 style="display: inline; margin-right:8px;">' . $this->paginanaam . '</h1>';
        static::toonIndienAanwezigEnAdmin($this->titelknoppen, '<div class="btn-group" style="vertical-align: bottom; margin-bottom: 3px;">', '</div>');
        echo "</div>\n";
    }

    public function isVoorPagina(): bool
    {
        if (substr($_SERVER['REQUEST_URI'], -1) == '/')
        {
            return true;
        }
        return false;
    }

    protected function toonMenu()
    {
        $websitelogo = '';//Instelling::geefInstelling('websitelogo');
        $inverseClass = '';//(Instelling::geefInstelling('menuthema') == 'donker') ? 'navbar-inverse' : '';
        $navbar = $websitelogo ? sprintf('<img alt="" src="%s"> ', $websitelogo) : $this->websitenaam;
        ?>
        <nav class="menu navbar <?= $inverseClass; ?>">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Navigatie omschakelen</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./"><?= $navbar; ?></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">

                        <?php
                        $menuarray = $this->geefMenu();
                        // Laat eerste menuonderdeel weg, daar is het logo voor.
                        //array_shift($menuarray);

                        if (count($menuarray) > 0)
                        {
                            foreach ($menuarray as $menuitem)
                            {
                                if (strpos($menuitem['link'], 'tooncategorie') !== false && strpos($menuitem['link'], '#dd') !== false)
                                {
                                    $id = intval(str_replace(['tooncategorie.php?id=', '#dd'], '', $menuitem['link']));
                                    global $pdo;

                                    $paginasInCategorie = $pdo->prepare("
										SELECT * FROM
										(
											SELECT 'sub' AS type, id, naam FROM subs WHERE categorieid=?
											UNION
											SELECT 'fotoboek' AS type, id, naam FROM fotoboeken WHERE categorieid=?
										) AS een
										ORDER BY naam ASC;");
                                    $paginasInCategorie->execute([$id, $id]);

                                    echo '<li class="dropdown">';

                                    echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . $menuitem['naam'] . ' <span class="caret"></span></a>';
                                    echo '<ul class="dropdown-menu">';

                                    foreach ($paginasInCategorie->fetchAll() as $pagina)
                                    {
                                        $url = new Url(sprintf('toon%s.php?id=%d', $pagina['type'], $pagina['id']));
                                        $link = $url->geefFriendly();
                                        printf('<li><a href="%s">%s</a></li>', $link, $pagina['naam']);
                                    }

                                    echo '</ul></li>';
                                }
                                else
                                {
                                    if ($this->menuItemIsHuidigePagina($menuitem['link']))
                                    {
                                        echo '<li class="active">';
                                    }
                                    else
                                    {
                                        echo '<li>';
                                    }

                                    echo '<a href="' . $menuitem['link'] . '">' . $menuitem['naam'] . '</a></li>';
                                }
                            }
                        }
                        ?>

                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a title="Instellingen aanpassen" href="configuratie.php">
                            <span class="glyphicon glyphicon-cog"></span>
                        </a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <?php
        $meldingen = Gebruiker::geefMeldingen();
        if ($meldingen)
        {
            echo '<div class="meldingencontainer">';
            echo '<div class="meldingen alert alert-info"><ul>';

            foreach ($meldingen as $melding)
            {
                echo '<li>' . $melding . '</li>';
            }

            echo '</ul></div></div>';
        }
    }

    private function menuItemIsHuidigePagina(string $menuItem): bool
    {
        // Vergelijking na || betekent testen of de hoofdurl is opgevraagd
        if ($menuItem == basename(substr($_SERVER['REQUEST_URI'], 1)) || ($menuItem == './' && substr($_SERVER['REQUEST_URI'], -1) == '/'))
        {
            return true;
        }

        return false;
    }

    public function toonPostPagina()
    {
        // Eerste div: inhoud. Tweede div: inhoudcontainer. Derde div: paginacontainer
        ?>
        </div></div></div>

        <script type="text/javascript" src="sys/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="sys/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="sys/js/cyndaron.js"></script>
    <?php
    foreach ($this->extraScripts as $extraScript)
    {
        printf('<script type="text/javascript" src="%s"></script>', $extraScript);
    }
    ?>

        </body>
        </html>
        <?php
    }

    public function voegScriptToe($scriptnaam)
    {
        $this->extraScripts[] = $scriptnaam;
    }

    public function geefMenu()
    {
        return [];
        global $pdo;
        $menu = $pdo->prepare('SELECT * FROM menu ORDER BY volgorde ASC;');
        $menu->execute();
        $menuitems = null;
        $eersteitem = true;

        foreach ($menu->fetchAll() as $menuitem)
        {
            $url = new Url($menuitem['link']);

            if ($menuitem['alias'])
            {
                $menuitem['naam'] = strtr($menuitem['alias'], [' ' => '&nbsp;']);
            }
            else
            {
                $menuitem['naam'] = $url->geefPaginanaam();
            }

            if ($eersteitem)
            {
                // De . is nodig omdat het menu anders niet goed werkt in subdirectories.
                $menuitem['link'] = './';
            }
            else
            {
                $menuitem['link'] = $url->geefFriendly();
            }
            $menuitems[] = $menuitem;
            $eersteitem = false;
        }
        return $menuitems;
    }

    public static function toonIndienAanwezig($string, $voor = null, $na = null)
    {
        if ($string)
        {
            echo $voor;
            echo $string;
            echo $na;
        }
    }

    public static function toonIndienAanwezigEnAdmin($string, $voor = null, $na = null)
    {
        if (Gebruiker::isAdmin() && $string)
        {
            echo $voor;
            echo $string;
            echo $na;
        }
    }

    public static function toonIndienAanwezigEnGeenAdmin($string, $voor = null, $na = null)
    {
        if (!Gebruiker::isAdmin() && $string)
        {
            echo $voor;
            echo $string;
            echo $na;
        }
    }
}