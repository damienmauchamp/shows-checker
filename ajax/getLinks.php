<?php
require_once dirname(__DIR__) . "/start.php";

//use TVShowsAPI\DB as db;
use TVShowsAPI\ZoneTelechargement as zt;
header('Content-type: application/json');

$_VAL = $_POST;
$_VAL = $_GET;
global $baseLinks;
$tvShowLinks = array();

$id = isset($_VAL["id"]) ? $_VAL["id"] : null;
$lastSeason = isset($_VAL["lastSeason"]) ? $_VAL["lastSeason"] : null;
$lastEpisode = isset($_VAL["lastEpisode"]) ? $_VAL["lastEpisode"] : null;
$quality = isset($_VAL["quality"]) ? $_VAL["quality"] : null;
$seen = isset($_VAL["seen"]) ? $_VAL["seen"] : null;


$db->updateShow($id, $lastSeason, $lastEpisode);
$less = intval($seen) === 1 ? 0 : -1;
$lastEpisode += $less;
// Suppression des anciens liens
$db->removeShowsOldLinks($id, $lastSeason, $lastEpisode);
$tvShow = new \TVShowsAPI\TVShow($id, $lastSeason, $lastEpisode);

// Recherche des liens et mise en BD
if ($tvShow->isOn()) {
    $tvShowId = $tvShow->getId();
    $tvShowName = $tvShow->getName();
    $tvNumberOfSeasons = $tvShow->getNumberOfSeasons();
    $tvSeasons = $tvShow->getSeasons();
    $myLastSeenSeason = $tvShow->getLastSeenSeason();
    $myLastSeenEpisode = $tvShow->getLastSeenEpisode();
    $tvShowQuality = isset($quality) ? $quality : 720;

    // SEASONS
    foreach ($tvSeasons as $tvSeason) {

        // on vérifie qu'on a pas déjà vu la saison
        if ($tvSeason->season_number >= $myLastSeenSeason) {
            $season = new \TVShowsAPI\TVSeason($tvSeason->id, $tvShowId, $tvSeason->season_number, $tvSeason->episode_count, strtotime($tvSeason->air_date));
            $seasonNumber = $season->getNumber();

            // on vérifie que la saison est disponible
            if (!$season->isWatched($myLastSeenSeason) && $season->isAvailable()) {
                $seasonEpisodes = $season->getEpisodes();

                // ÉPISODES
                foreach ($seasonEpisodes as $tvEpisode) {

                    $episode = new \TVShowsAPI\TVEpisode($tvEpisode->id, $tvShowId, $season->getNumber(), $tvEpisode->episode_number, strtotime($tvEpisode->air_date));
                    $episodeAirDate = $episode->getAirDate();
                    $episodeNumber = $episode->getNumber();

                    // on vérifie qu'on a pas déjà vu l'épisode et que l'épisode est disponible
                    if (!$episode->isWatched($myLastSeenSeason, $myLastSeenEpisode) && $episode->isAvailable()) {

                        if (!isset($baseLinks[$tvShowId][$seasonNumber][$episodeNumber]) || $baseLinks[$tvShowId][$seasonNumber][$episodeNumber] == null) {
                            $tvShowLinks[$seasonNumber][$episodeNumber] = zt::getTvEpisodeLink($tvShowName, $episode, $tvShowQuality);
                        }
                    }
                }
            }
        }
    }
    $db->addShowsLinks($id, $tvShowLinks);
    echo $db->getShowsLink($id);
}
