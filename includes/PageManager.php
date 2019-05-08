<?php

/**
 * @author Kristian Stöckel https://github.com/MrKrisKrisu
 */
class PageManager {

    public function __construct() {
        ob_start();
        require_once self::getRoutingFile();
        if (isset($_REQUEST['ajax']))
            return;
        $content = ob_get_clean();
        ob_end_clean();

        self::header();
        echo $content;
        self::footer();
    }

    private static function getRoutingFile() {
        $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $file = BASE_PATH . '/pageDir' . $uri . 'index.php';
        $fileAjax = BASE_PATH . '/pageDir' . $uri . 'ajax.php';
        if (isset($_REQUEST['ajax']) && file_exists($fileAjax))
            return $fileAjax;
        if (file_exists($file))
            return $file;
        return self::get404File();
    }

    private static function get404File() {
        return BASE_PATH . '/pageDir/404.php';
    }

    private static function header() {
        ?>
        <!doctype html>
        <html lang="de">
            <head>
                <meta charset="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
                <title>Zahl mit Karte</title>

                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

                <link href="/style.css" rel="stylesheet" />
                <link href="/leaflet/leaflet.css" rel="stylesheet" />
                <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
                <script src="/leaflet/leaflet.js"></script>
            </head>
            <body>
                <header>
                    <div class="navbar navbar-dark bg-dark shadow-sm">
                        <div class="container d-flex justify-content-between">
                            <a href="/" class="navbar-brand d-flex align-items-center">
                                <strong>Zahl mit Karte!</strong>
                            </a>
                        </div>
                    </div>
                </header>

                <main role="main">
                    <div class="container" style="padding-top: 15px;">
                        <?php
                    }

                    private static function footer() {
                        ?>
                    </div>
                </main>

                <footer class="text-muted">
                    <div class="container">
                    </div>
                </footer>
            </body>
            <!-- Matomo -->
            <script type="text/javascript">
                var _paq = window._paq || [];
                /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
                _paq.push(['trackPageView']);
                _paq.push(['enableLinkTracking']);
                (function () {
                    var u = "//uphuupskisdywjpnanoimsrqw.k118.de/";
                    _paq.push(['setTrackerUrl', u + 'matomo.php']);
                    _paq.push(['setSiteId', '10']);
                    var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
                    g.type = 'text/javascript';
                    g.async = true;
                    g.defer = true;
                    g.src = u + 'matomo.js';
                    s.parentNode.insertBefore(g, s);
                })();
            </script>
            <!-- End Matomo Code -->

        </html>
        <?php
    }

}
