<?php
include_once("config/config.php");
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
