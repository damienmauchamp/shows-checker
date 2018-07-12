<?php
require_once "../start.php";

header("Content-type:application/json");
use TVShowsAPI\APICall as api;

$id = $_GET["id"];
$lastSeenSeason = $_GET["lastSeenSeason"];
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
    if ($i === intval($lastSeenSeason))
        $poster = $val->poster_path;
}

echo json_encode(array(
    "id" => $id,
    "name" => $name,
    "seasons" => $seasons,
    "poster" => $poster
));

/*
vardump($id);
vardump($name);
vardump($seasons);
$prePoster = "//image.tmdb.org/t/p/w600_and_h900_bestv2";
vardump($prePoster . $poster);

echo "<img src=\"$prePoster$poster\"/>";
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
*/