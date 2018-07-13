<?php
/**
 * Récupération des liens
 * À boucler en ajax
 */
require_once dirname(__DIR__) . "/start.php";

header('Content-type: application/json');

$id = isset($_GET["id"]) ? $_GET["id"] : null;
$lastSeason = isset($_GET["lastSeenSeason"]) ? $_GET["lastSeenSeason"] : null;
$lastEpisode = isset($_GET["lastSeenEpisode"]) ? $_GET["lastSeenEpisode"] : null;

$show = $db->getShow($id);
// todo : si >
$episodes = $show->getNeededEpisodes($lastSeason, $lastEpisode);
$links = fetchLinks($show, $episodes);
$res = $show->updateLinks($links);
$prog = $show->updateProgression($lastSeason, $lastEpisode);
$rem = $show->removeOldLinks();
echo json_encode($links);