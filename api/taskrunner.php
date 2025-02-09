<?php
include_once("lib/appLibLoader.php");

$db = Database::getInstance();
$alertService = new AlertService($db->getPdoConnection());
$alertService->checkEnergyData();
die;
