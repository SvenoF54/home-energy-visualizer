<?php
if ($_GET["apikey"] != "shellyuni-batterie-garage") {
	return;
}
$powerStatus = $_GET["status"] == "ON";
$subject = "Turned Deye 300: ". ($powerStatus ? "ON" : "OFF") . " by Shelly-Uni";
$body = var_export($_GET, true);


mail('meine@mail.xy', $subject, $body);


?>