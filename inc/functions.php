<?php

use TVShowsAPI\DB as db;
use TVShowsAPI\APICall as api;
use TVShowsAPI\ZoneTelechargement as zt;

function fetchShow($id, $lastSeenSeason)
{
    $maxSeasons = 20;

    $url = "https://api.themoviedb.org/3/tv/" . $id . "?api_key=" . API_KEY . "&append_to_response=";
    for ($i = 1; $i <= $maxSeasons; $i++) {
        $url .= "season/$i";
        $url .= $i !== $maxSeasons ? "," : null;
    }
    $response = json_decode($json = api::get($url));
//echo $json;exit;

    $name = $response->name;
    $number_of_seasons = $response->number_of_seasons;
    $poster = $response->poster_path;

    $seasons = array();
    for ($i = 1; $i <= $number_of_seasons; $i++) {
        $key = "season/$i";
        $val = isset($response->$key) ? $response->$key : null;
        $seasons[$i] = count($val->episodes);
        if ($i === intval($lastSeenSeason) && $val->poster_path) {
            $poster = $val->poster_path;
        }
    }

    return json_encode(array(
        "id" => $id,
        "name" => $name,
        "seasons" => $seasons,
        "poster" => $poster
    ));
}

function getShows()
{
    $db = new db;
    /** @var \TVShowsAPI\TVShow $show */
    foreach ($db->getShows() as $show) {
//        var_dump($show);
        echo $show->toString();
    }
}

/**
 * @param \TVShowsAPI\TVShow $show
 * @param $episodes
 * @return array
 */
function fetchLinks($show, $episodes)
{
    $finalLink = array();

    foreach ($episodes as $season => $eps) {
        $searchUrl = zt::search($show->getName() . " saison $season");
        $searchContent = zt::curlGETRequestToHtml($searchUrl);
        $searchLinks = zt::getResultLinks($searchContent);
        $article = null;
        foreach ($searchLinks as $l) {
            if (strpos($l, "saison-$season")) {
                $article = $l;
                break;
            }
        }
        $dlProtectLinks = zt::getSeasonLinks($article, $eps);
        $finalLink[$season] = zt::getFinalLinks($dlProtectLinks, "1fichier");
    }
    return $finalLink;
}