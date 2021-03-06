<?php

ini_set('xdebug.max_nesting_level', 2000);
ini_set('max_input_time', 10800);
ini_set('max_execution_time', 10800);
ini_set('session.gc_maxlifetime', 10800);
ini_set("max_execution_time", 0);
set_time_limit(0);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . "/inc/functions.php";

use \TVShowsAPI\APICall as api;
use \TVShowsAPI\DB as db;

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
$db = new db;

// all shows
//$tvShowsList = array();
//$tvShowsList = $db->getShows();
//
//// all links
//$baseLinks = array();
//$baseLinks = $db->getLinks();

$tvLinks = array();

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

function linksToHTML($array)
{
    $text = "S$array[season]E$array[episode]";
    return $array["link"] ?
        "<a href=\"$array[link]\" class=\"episode-link\" data-link-season=\"$array[season]\"  data-link-episode=\"$array[episode]\">$text</a><br/>" :
        $text . "<br/>";
}

// init
if (false) {
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

//    $db = new db;
//    $db->showsInit($tvShowsList);
//    exit;
}


function vardump($data, $label = '', $return = false)
{

    $debug = debug_backtrace();
    $callingFile = $debug[0]['file'];
    $callingFileLine = $debug[0]['line'];

    ob_start();
    var_dump($data);
    $c = ob_get_contents();
    ob_end_clean();

    $c = preg_replace("/\r\n|\r/", "\n", $c);
    $c = str_replace("]=>\n", '] = ', $c);
    $c = preg_replace('/= {2,}/', '= ', $c);
    $c = preg_replace("/\[\"(.*?)\"\] = /i", "[$1] = ", $c);
    $c = preg_replace('/  /', "    ", $c);
    $c = preg_replace("/\"\"(.*?)\"/i", "\"$1\"", $c);
    $c = preg_replace("/(int|float)\(([0-9\.]+)\)/i", "$1() <span class=\"number\">$2</span>", $c);

// Syntax Highlighting of Strings. This seems cryptic, but it will also allow non-terminated strings to get parsed.
    $c = preg_replace("/(\[[\w ]+\] = string\([0-9]+\) )\"(.*?)/sim", "$1<span class=\"string\">\"", $c);
    $c = preg_replace("/(\"\n{1,})( {0,}\})/sim", "$1</span>$2", $c);
    $c = preg_replace("/(\"\n{1,})( {0,}\[)/sim", "$1</span>$2", $c);
    $c = preg_replace("/(string\([0-9]+\) )\"(.*?)\"\n/sim", "$1<span class=\"string\">\"$2\"</span>\n", $c);

    $regex = array(
        // Numberrs
        'numbers' => array('/(^|] = )(array|float|int|string|resource|object\(.*\)|\&amp;object\(.*\))\(([0-9\.]+)\)/i', '$1$2(<span class="number">$3</span>)'),
        // Keywords
        'null' => array('/(^|] = )(null)/i', '$1<span class="keyword">$2</span>'),
        'bool' => array('/(bool)\((true|false)\)/i', '$1(<span class="keyword">$2</span>)'),
        // Types
        'types' => array('/(of type )\((.*)\)/i', '$1(<span class="type">$2</span>)'),
        // Objects
        'object' => array('/(object|\&amp;object)\(([\w]+)\)/i', '$1(<span class="object">$2</span>)'),
        // Function
        'function' => array('/(^|] = )(array|string|int|float|bool|resource|object|\&amp;object)\(/i', '$1<span class="function">$2</span>('),
    );

    foreach ($regex as $x) {
        $c = preg_replace($x[0], $x[1], $c);
    }

    $style = '
/* outside div - it will float and match the screen */
.dumpr {
    margin: 2px;
    padding: 2px;
    background-color: #fbfbfb;
    float: left;
    clear: both;
}
/* font size and family */
.dumpr pre {
    color: #000000;
    font-size: 9pt;
    font-family: "Courier New",Courier,Monaco,monospace;
    margin: 0px;
    padding-top: 5px;
    padding-bottom: 7px;
    padding-left: 9px;
    padding-right: 9px;
}
/* inside div */
.dumpr div {
    background-color: #fcfcfc;
    border: 1px solid #d9d9d9;
    float: left;
    clear: both;
}
/* syntax highlighting */
.dumpr span.string {color: #c40000;}
.dumpr span.number {color: #ff0000;}
.dumpr span.keyword {color: #007200;}
.dumpr span.function {color: #0000c4;}
.dumpr span.object {color: #ac00ac;}
.dumpr span.type {color: #0072c4;}
';

    $style = preg_replace("/ {2,}/", "", $style);
    $style = preg_replace("/\t|\r\n|\r|\n/", "", $style);
    $style = preg_replace("/\/\*.*?\*\//i", '', $style);
    $style = str_replace('}', '} ', $style);
    $style = str_replace(' {', '{', $style);
    $style = trim($style);

    $c = trim($c);
    $c = preg_replace("/\n<\/span>/", "</span>\n", $c);

    if ($label == '') {
        $line1 = '';
    } else {
        $line1 = "<strong>$label</strong> \n";
    }

    $out = "\n<!-- Dumpr Begin -->\n" .
        "<style type=\"text/css\">" . $style . "</style>\n" .
        "<div class=\"dumpr\">
    <div><pre>$line1 $callingFile : $callingFileLine \n$c\n</pre></div></div><div style=\"clear:both;\">&nbsp;</div>" .
        "\n<!-- Dumpr End -->\n";
    if ($return) {
        return $out;
    } else {
        echo $out;
    }
}
