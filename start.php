<?php

ini_set('xdebug.max_nesting_level', 2000);
ini_set('max_input_time', 10800);
ini_set('max_execution_time', 10800);
ini_set('session.gc_maxlifetime', 10800);
ini_set("max_execution_time", 0);
set_time_limit(0);

require __DIR__ . '/vendor/autoload.php';

use \TVShowsAPI\APICall as api;
//use \TVShowsAPI\DB as db;

$keys = explode(":", file_get_contents(__DIR__ . '/.keys'));
define("API_URL", "https://api.themoviedb.org/3/");
// Clé de l'API (v3 auth)
define("API_KEY", $keys[0]);
// Jeton d'accès en lecture à l'API (v4 auth)
define("API_TOKEN", $keys[1]);
define("URL_TV_SHOW", "tv/");

/*
 * TODO : DB
 * INSERT INTO table (id, name, age) VALUES(1, "A", 19) ON DUPLICATE KEY UPDATE   name="A", age=19
 *
 * INSERT INTO links ( id, saison, episode, lien ) VALUES (A, B, C, D) ON DUPLICATE KEY UPDATE id=A, saison=B, episode=C
 *
 * SHOWS ( id, nom, lastSeenSeason, lastSeenEpisode, actif ) ==> $tvShowsList
 * LINKS ( id, saison, episode, lien ) ==> $tvLinks
 *
 * Btn DL, on click :
 *      - suppr le lien{links} dans la db
 *      - SHOWS -> change les {lastSeenSeason, lastSeenEpisode}
 */


// vide
//$db = new db;
$tvShowsList = array();
//$tvShowsList = $db->getShows();
$baseLinks = array();
//$baseLinks = $db->getLinks();
$tvLinks = array();
//var_dump($tvLinks);
//exit;

function getTvShow($id)
{
    $url = API_URL . URL_TV_SHOW . $id;
    return api::get($url);
}

function getTvShowSeason($id, $season)
{
    $url = API_URL . URL_TV_SHOW . $id . "/season/$season";
    return api::get($url);
}

function getTvShowSeasonEpisodes($id, $season)
{
    $response = json_decode(getTvShowSeason($id, $season));
    return isset($response->episodes) ? $response->episodes : null;
}

if (true) {
    $tvShowsList = array(
        array(
            "id" => 60948,
            "name" => "12 Monkeys",
            "lastSeenSeason" => "4",
            "lastSeenEpisode" => "3",
            "status" => true
        ),
        array(
            "id" => 10283,
            "name" => "Archer",
            "lastSeenSeason" => "9",
            "lastSeenEpisode" => "8",
            "status" => true
        ),
        array(
            "id" => 1412,
            "name" => "Arrow",
            "lastSeenSeason" => "6",
            "lastSeenEpisode" => "23",
            "status" => true
        ),
        array(
            "id" => 65495,
            "name" => "Atlanta",
            "lastSeenSeason" => "2",
            "lastSeenEpisode" => "10",
            "status" => true
        ),
        array(
            "id" => 71663,
            "name" => "Black Lightning",
            "lastSeenSeason" => "1",
            "lastSeenEpisode" => "13",
            "status" => true
        ),
        array(
            "id" => 62710,
            "name" => "Blindspot",
            "lastSeenSeason" => "3",
            "lastSeenEpisode" => "22",
            "status" => true
        ),
        array(
            "id" => 62858,
            "name" => "Colony",
            "lastSeenSeason" => "3",
            "lastSeenEpisode" => "8",
            "status" => true
        ),
        array(
            "id" => 62643,
            "name" => "DC's Legends of Tomorrow",
            "lastSeenSeason" => "3",
            "lastSeenEpisode" => "18",
            "status" => true
        ),
        array(
            "id" => 1399,
            "name" => "Game of Thrones",
            "lastSeenSeason" => "7",
            "lastSeenEpisode" => "7",
            "quality" => "1080",
            "status" => true
        ),
        array(
            "id" => 60708,
            "name" => "Gotham",
            "lastSeenSeason" => "4",
            "lastSeenEpisode" => "22",
            "status" => true
        ),
        array(
            "id" => 47450,
            "name" => "Into the Badlands",
            "lastSeenSeason" => "2",
            "lastSeenEpisode" => "10",
            "status" => true
        ),
        array(
            "id" => 71340,
            "name" => "Krypton",
            "lastSeenSeason" => "1",
            "lastSeenEpisode" => "2",
            "status" => true
        ),
        array(
            "id" => 67195,
            "name" => "Legion",
            "lastSeenSeason" => "2",
            "lastSeenEpisode" => "11",
            "status" => true
        ),
        array(
            "id" => 63174,
            "name" => "Lucifer",
            "lastSeenSeason" => "3",
            "lastSeenEpisode" => "24",
            "status" => true
        ),
        array(
            "id" => 1403,
            "name" => "Marvel's Agents of S.H.I.E.L.D.",
            "lastSeenSeason" => "5",
            "lastSeenEpisode" => "22",
            "status" => true
        ),
        array(
            "id" => 66190,
            "name" => "Marvel's Cloak & Dagger",
            "lastSeenSeason" => "1",
            "lastSeenEpisode" => "0",
            "status" => true
        ),
        array(
            "id" => 61889,
            "name" => "Marvel's Daredevil",
            "lastSeenSeason" => "2",
            "lastSeenEpisode" => "13",
            "status" => true
        ),
        array(
            "id" => 38472,
            "name" => "Marvel's Jessica Jones",
            "lastSeenSeason" => "2",
            "lastSeenEpisode" => "13",
            "status" => true
        ),
        array(
            "id" => 62127,
            "name" => "Marvel's Iron Fist",
            "lastSeenSeason" => "1",
            "lastSeenEpisode" => "13",
            "status" => true
        ),
        array(
            "id" => 62126,
            "name" => "Marvel's Luke Cage",
            "lastSeenSeason" => "2",
            "lastSeenEpisode" => "5",
            "status" => true
        ),
        array(
            "id" => 62285,
            "name" => "Marvel's The Defenders",
            "lastSeenSeason" => "1",
            "lastSeenEpisode" => "8",
            "status" => true
        ),
        array(
            "id" => 67466,
            "name" => "Marvel's Runaways",
            "lastSeenSeason" => "1",
            "lastSeenEpisode" => "10",
            "status" => true
        ),
        array(
            "id" => 62560,
            "name" => "Mr. Robot",
            "lastSeenSeason" => "3",
            "lastSeenEpisode" => "10",
            "status" => true
        ),
        array(
            "id" => 64230,
            "name" => "Preacher",
            "lastSeenSeason" => "3",
            "lastSeenEpisode" => "1",
            "status" => true
        ),
        array(
            "id" => 62816,
            "name" => "Quantico",
            "lastSeenSeason" => "3",
            "lastSeenEpisode" => "4",
            "status" => true
        ),
        array(
            "id" => 60625,
            "name" => "Rick and Morty",
            "lastSeenSeason" => "3",
            "lastSeenEpisode" => "10",
            "status" => true
        ),
        array(
            "id" => 62823,
            "name" => "Scream",
            "lastSeenSeason" => "2",
            "lastSeenEpisode" => "12",
            "status" => true
        ),
        array(
            "id" => 62688,
            "name" => "Supergirl",
            "lastSeenSeason" => "3",
            "lastSeenEpisode" => "23",
            "status" => true
        ),
        array(
            "id" => 65708,
            "name" => "Taboo",
            "lastSeenSeason" => "1",
            "lastSeenEpisode" => "8",
            "status" => true
        ),
        array(
            "id" => 48866,
            "name" => "The 100",
            "lastSeenSeason" => "5",
            "lastSeenEpisode" => "8",
            "status" => true
        ),
        array(
            "id" => 46952,
            "name" => "The Blacklist",
            "lastSeenSeason" => "5",
            "lastSeenEpisode" => "22",
            "status" => true
        ),
        array(
            "id" => 60735,
            "name" => "The Flash",
            "lastSeenSeason" => "4",
            "lastSeenEpisode" => "23",
            "status" => true
        ),
        array(
            "id" => 69629,
            "name" => "The Gifted",
            "lastSeenSeason" => "1",
            "lastSeenEpisode" => "13",
            "status" => true
        ),
        array(
            "id" => 62017,
            "name" => "The Man in the High Castle",
            "lastSeenSeason" => "2",
            "lastSeenEpisode" => "10",
            "status" => true
        ),
        array(
            "id" => 1402,
            "name" => "The Walking Dead",
            "lastSeenSeason" => "8",
            "lastSeenEpisode" => "16",
            "quality" => "1080",
            "status" => true
        ),
        array(
            "id" => 46648,
            "name" => "True Detective",
            "lastSeenSeason" => "2",
            "lastSeenEpisode" => "8",
            "status" => true
        ),
        array(
            "id" => 63247,
            "name" => "Westworld",
            "lastSeenSeason" => "2",
            "lastSeenEpisode" => "5",
            "status" => true
        ),
        array(
            "id" => 62117,
            "name" => "Younger",
            "lastSeenSeason" => "5",
            "lastSeenEpisode" => "3",
            "status" => true
        ),
        array(
            "id" => 60866,
            "name" => "iZombie",
            "lastSeenSeason" => "4",
            "lastSeenEpisode" => "13",
            "quality" => "1080",
            "status" => true
        ),
    );

//    $db = new db();
//    $db->showsInit($tvShowsList);
//    exit;
}
