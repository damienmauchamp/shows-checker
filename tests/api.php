<?php
require_once "../start.php";

$id = isset($_GET["id"]) ? $_GET["id"] : null;
$lastSeenSeason = isset($_GET["lastSeenSeason"]) ? $_GET["lastSeenSeason"] : null;
if (!($id && $lastSeenSeason)) exit;

header("Content-type:application/json");

use TVShowsAPI\APICall as api;

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
    if ($i === intval($lastSeenSeason) && $val->poster_path)
        $poster = $val->poster_path;
}

echo json_encode(array(
    "id" => $id,
    "name" => $name,
    "seasons" => $seasons,
    "poster" => $poster
));