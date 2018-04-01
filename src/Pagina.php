<?php
namespace Kaindar;

use Cyndaron\DBConnection;
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
            printf('<link href="/vendor/FontAwesome/css/font-awesome.css?r=%s" type="text/css" rel="stylesheet" />', CyndaronInfo::ENGINE_VERSIE);
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

        echo '<div class="paginatitel ' . $class . '"><h1>' . $this->paginanaam . '</h1>';
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
        $alleRekeningen = Util::geefAlleRekeningen();

        ?>
        <nav class="navbar menu navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="/">Administratie</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Rek. bijwerken
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                            foreach ($alleRekeningen as $rekening)
                            {
                                $jaar = DBConnection::doQueryAndFetchOne('SELECT MAX(jaar) FROM (SELECT DATE_FORMAT(datum, "%Y") as jaar FROM mutaties WHERE rekening=?) AS een', [$rekening['afkorting']]);
                                printf('<a class="dropdown-item" href="/rekeningbijwerken?afkorting=%s&amp;toonjaar=%d">%s</a>', $rekening['afkorting'], $jaar, $rekening['omschrijving']);
                            }
                            ?>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Maandsaldi
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                            foreach ($alleRekeningen as $rekening)
                            {
                                printf('<a class="dropdown-item" href="/saldioverzicht?afkorting=%s">%s</a>', $rekening['afkorting'], $rekening['omschrijving']);
                            }
                            echo '<a class="dropdown-item" href="/saldioverzicht">Alle rekeningen</a>';
                            ?>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Opget. posten
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                            foreach ($alleRekeningen as $rekening)
                            {
                                printf('<a class="dropdown-item" href="/postoverzicht?afkorting=%s">%s</a>', $rekening['afkorting'], $rekening['omschrijving']);
                            }
                            echo '<a class="dropdown-item" href="/postoverzicht">Alle rekeningen</a>';
                            ?>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Speciale overzichten
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/btw">BTW</a>
                            <a class="dropdown-item" href="/contributieoverzicht">Contributie</a>
                            <a class="dropdown-item" href="/grootboek">Grootboek</a>
                            <a class="dropdown-item" href="/resultatenrekening">Resultatenrekening</a>
                            <a class="dropdown-item" href="/inkomstenuitgaven">Staat van inkomsten en uitgaven</a>
                        </div>
                    </li>
                </ul>

                <div>
                    <a href="/instellingen"><span class="fa fa-cog"></span> Instellingen</a> &nbsp;
                    <a href="/codes"><span class="fa fa-barcode"></span> Codes</a>
                </div>

            </div>
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

        <script type="text/javascript" src="/vendor/jQuery/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="/vendor/Popper.js/popper.min.js"></script>
        <script type="text/javascript" src="/vendor/Bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/kaindar.js"></script>
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

    public static function toonIndienAanwezig($string, $voor = null, $na = null)
    {
        if ($string)
        {
            echo $voor;
            echo $string;
            echo $na;
        }
    }
}