<?php
/**
 * Récupération des informations de toutes les séries
 * À lancer en background
 */
require_once dirname(__DIR__) . "/start.php";

use TVShowsAPI\TVShow as TVShow;
use TVShowsAPI\DB as db;

$db = new db;

/** @var TVShow $tvShow */
foreach ($db->getShows() as $tvShow) {
//    var_dump($tvShow);exit;
    $id = $tvShow->getId();
    $lastSeenSeason = $tvShow->getLastSeenSeason();
    $lastSeenEpisode = $tvShow->getLastSeenEpisode();
    $res = json_decode(fetchShow($id, $lastSeenSeason));
    $name = $res->name;
    $poster = $res->poster;
    $seasons = $res->seasons;

    $show = TVShow::withArray(array(
        "id" => $id,
        "name" => $name,
        "lastSeenSeason" => $lastSeenSeason,
        "lastSeenEpisode" => $lastSeenEpisode,
        "poster" => $poster,
        "quality" => "720",
        "status" => true,
        "seasons" => $seasons
    ));
    $show->addToDatabase();
}