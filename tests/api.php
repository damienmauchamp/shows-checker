<?php
require_once "../start.php";

use TVShowsAPI\APICall as api;

$id = 60948;
$n = 20;
$url = "https://api.themoviedb.org/3/tv/".$id."?api_key=".API_KEY."&append_to_response=";
for ($i = 1 ; $i <= $n ; $i++) {
    $url.= "season/$i";
    $url.= $i !== $n ? "," : null;
}
//echo $url;exit;
$response = json_decode($json = api::get($url));

// nombre de saisons
$number_of_seasons = $response->number_of_seasons;
//echo $number_of_seasons;

// nombre d'épisodes par saisons
for ($i = 0 ; $i <= $number_of_seasons ; $i++) {
    $key = "season/$i";
    var_dump(isset($response->$key) ? $response->$key : null);
}
//foreach ($number_of_seasons as $n) {
//    $key = "season/$n";
//    echo $response->$key;break;
//}
//echo $json;

