<?php

require_once "start.php";
//file_put_contents("aaa.txt", date("Y-m-d H:i:s")." \t start\n\n ", FILE_APPEND);

use \TVShowsAPI\ZoneTelechargement as zt;

global $baseLinks, $tvShowsList, $tvLinks;

//$newLinks = array();
//$newLinks[62858][3][3] = "yguhkiljomk";
//$newLinks[62858][3][2] = "estrdiljomk";
//$newLinks[62858][3][1] = "estrdfyguhk";
//var_dump($baseLinks);
//var_dump($newLinks);


foreach ($tvShowsList as $s) {

    $tvShow = new \TVShowsAPI\TVShow($s['id'], $s['lastSeenSeason'], $s['lastSeenEpisode'], $s["status"]);
    if ($tvShow->isOn()) {
        $tvShowId = $tvShow->getId();
        $tvShowName = $tvShow->getName();
        $tvNumberOfSeasons = $tvShow->getNumberOfSeasons();
        $tvSeasons = $tvShow->getSeasons();
        $myLastSeenSeason = $tvShow->getLastSeenSeason();
        $myLastSeenEpisode = $tvShow->getLastSeenEpisode();
        $tvShowQuality = isset($s["quality"]) ? $s["quality"] : 720;
        //var_dump("$tvShowId - $tvShowName");

        // SEASONS
        foreach ($tvSeasons as $tvSeason) {

            $season = new \TVShowsAPI\TVSeason($tvSeason->id, $tvShowId, $tvSeason->season_number, $tvSeason->episode_count, strtotime($tvSeason->air_date));
//        $seasonId = $season->getId();
            $seasonNumber = $season->getNumber();
//        $seasonEpisodesCount = $season->getEpisodesCount();
//        $seasonAirDate = $season->getAirDate();

            if (!$season->isWatched($myLastSeenSeason) && $season->isAvailable()) {
                $seasonEpisodes = $season->getEpisodes();

                // ÉPISODES
                foreach ($seasonEpisodes as $tvEpisode) {
                    $episode = new \TVShowsAPI\TVEpisode($tvEpisode->id, $tvShowId, $season->getNumber(), $tvEpisode->episode_number, strtotime($tvEpisode->air_date));

                    $episodeAirDate = $episode->getAirDate();
                    $episodeNumber = $episode->getNumber();

                    if (!$episode->isWatched($myLastSeenSeason, $myLastSeenEpisode) && $episode->isAvailable()) {
                        if (!isset($baseLinks[$tvShowId][$seasonNumber][$episodeNumber]) || $baseLinks[$tvShowId][$seasonNumber][$episodeNumber] == null) {
                            $tvLinks[$tvShowId][$seasonNumber][$episodeNumber] = zt::getTvEpisodeLink($tvShowName, $episode, $tvShowQuality);
//                            var_dump($baseLinks[$tvShowId][$seasonNumber][$episodeNumber]);
                            //var_dump("aucun lien pour S" . sprintf("%02d", $seasonNumber) . "E" . sprintf("%02d", $episodeNumber));
//                        exit;
                        } /*else {
                            var_dump("OK S" . sprintf("%02d", $seasonNumber) . "E" . sprintf("%02d", $episodeNumber));
                        }*/
                    }
                }
            }
        }
//        if ($tvShowId === 62643)
//            break;
//        file_put_contents("aaa.txt", date("Y-m-d H:i:s")." \t $tvShowId\n\n ", FILE_APPEND);
    }
}

var_dump($tvLinks);

$newLinks = array();
foreach ($tvLinks as $id => $show) {
    foreach ($show as $season => $seasonEps) {
        foreach ($seasonEps as $episode => $link) {
            $newLink = array(
                "id" => $id,
                "season" => $season,
                "episode" => $episode,
                "link" => $link
            );
            if (!empty($id))
                $newLinks[] = $newLink;
        }
    }
}

//$db = new \TVShowsAPI\DB();
//$db->addLinks($newLinks);

var_dump($newLinks);