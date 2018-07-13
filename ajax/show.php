<?php
/**
 * Récupération des informations de la série
 */
require_once dirname(__DIR__) . "/start.php";

$id = isset($_GET["id"]) ? $_GET["id"] : null;
$lastSeenSeason = isset($_GET["lastSeenSeason"]) ? $_GET["lastSeenSeason"] : null;
if (!($id && $lastSeenSeason)) exit;

header("Content-type:application/json");

echo fetchShow($id, $lastSeenSeason);