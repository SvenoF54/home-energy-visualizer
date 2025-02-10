<?php
setlocale(LC_TIME, 'de_DE.UTF-8', 'de_DE', 'deu_deu');
date_default_timezone_set('Europe/Berlin');

include_once(__DIR__ ."/../lib/config/configuration.php");
include_once("local-config.php");  // Eine local.config.php Datei erstellen, s. local-config.php.sample

define('MONTH_LIST', [
    1 => "Januar", 2 => "Februar", 3 => "März", 4 => "April", 5 => "Mai", 6 => "Juni",
    7 => "Juli", 8 => "August", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Dezember"
]);
