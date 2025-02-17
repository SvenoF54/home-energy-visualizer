<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

// configure VIEW

$pageTitle = "Info";
$jsHeaderFiles = ["/js/utils.js"];
$jsFooterFiles = [];
$cssFiles = [];
$jsVars = [];

$partialTop = null;
$partialBottom = "views/pages/about/about.phtml";


include("views/partials/layout.phtml");

?>
