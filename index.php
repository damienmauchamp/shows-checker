<?php
$time_start = microtime(true);
set_time_limit(0);
require_once "start.php";
$root = '';
$refresh = (isset($_GET["page"]) && $_GET["page"] === "refresh");
?>
<!DOCTYPE html>
<html>
<head>
    <? include "inc/meta.php" ?>
</head>
<body>
    <a href="index.php">Vue d'ensemble</a>
    <a href="index.php?page=refresh">Refresh</a>
<?
if (!$refresh) {
    $links = json_decode($db->getAllLinks());
    $res = array();
    foreach ($links as $l) {
        $id = $l->id;
        $season = $l->season;
        $episode = $l->episode;
        $res[$id]["name"] = $l->name;
        $res[$id]["seasons"][$season][$episode]["link"] = $l->link;
        $res[$id]["seasons"][$season][$episode]["html"] = $l->html;
        $res[$id]["seasons"][$season][$episode]["quality"] = $l->quality;
    }

    foreach ($res as $show) { ?>
        <div class="">
            <h3 class=""><?= $show["name"] ?></h3>
            <div class="">
                <? foreach ($show["seasons"] as $n => $season) { ?>
                    <h4 class="">Saison <?= $n ?></h4>
                    <div class="">
                        <? foreach ($season as $e => $episode) { ?>
                            <div class="">Episode <?= $e ?>
                                <?= $episode["html"] ?>
                            </div>
                        <? } ?>
                    </div>
                <? } ?>
            </div>
        </div>
    <? }
//    echo "<pre>";
//    print_r($res);
//    echo "</pre>";
} else {
    global $baseLinks, $tvShowsList, $tvLinks;

    foreach ($tvShowsList as $s) {

        $tvShow = new \TVShowsAPI\TVShow($s['id'], $s['lastSeenSeason'], $s['lastSeenEpisode'], $s["status"]);

        if ($tvShow->isOn()) {
            $tvShowId = $tvShow->getId();
            $tvShowName = $tvShow->getName();
            $tvNumberOfSeasons = $tvShow->getNumberOfSeasons();
            $tvSeasons = $tvShow->getSeasons();
            $myLastSeenSeason = $tvShow->getLastSeenSeason();
            $myLastSeenEpisode = $tvShow->getLastSeenEpisode();
            $tvShowQuality = isset($s["quality"]) ? $s["quality"] : $db->getShowQuality($tvShowId);

            var_dump($tvShowName);
            $seasonsCount = array();

            // SEASONS
            foreach ($tvSeasons as $tvSeason) {
                $season = new \TVShowsAPI\TVSeason($tvSeason->id, $tvShowId, $tvSeason->season_number, $tvSeason->episode_count, strtotime($tvSeason->air_date));
                $seasonNumber = $season->getNumber();
                $numberOfEpisodes = $season->getEpisodesCount();

                $seasonsCount[$seasonNumber] = $numberOfEpisodes;

//                if (!$season->isWatched($myLastSeenSeason) && $season->isAvailable()) {
//                    $seasonEpisodes = $season->getEpisodes();
//                }
            }

            ?>
            <label for="seasons-<?= $tvShowId ?>"></label>
            <select class="seasons" data-show-id="<?= $tvShowId ?>" id="seasons-<?= $tvShowId ?>">
                <? foreach ($seasonsCount as $s => $n) {
                    $selected = $s == $myLastSeenSeason ? "selected" : "";
                    if ($s > 0) { ?>
                        <option data-season-episodes="<?= $n ?>" value="<?= $s ?>" <?= $selected ?>><?= $s ?></option>
                    <? }
                } ?>
            </select>
            <label for="episodes-<?= $tvShowId ?>"></label>
            <select class="episodes" data-show-id="<?= $tvShowId ?>" id="episodes-<?= $tvShowId ?>">
                <? for ($e = 1; $e <= $seasonsCount[$myLastSeenSeason]; $e++) {
                    $selected = $e == $myLastSeenEpisode ? "selected" : "";
                    if ($e > 0) { ?>
                        <option value="<?= $e ?>" <?= $selected ?>><?= $e ?></option>
                    <? }
                } ?>
            </select>
            <button class="btn-show" id="btn-<?= $tvShowId ?>"
                    data-show-id="<?= $tvShowId ?>"
                    data-show-last-season="<?= $myLastSeenSeason ?>"
                    data-show-last-episode="<?= $myLastSeenEpisode ?>"
                    data-show-quality="<?= $tvShowQuality ?>"
                    onclick="getLinks(this)"
                    value="<?= $tvShowName ?>">Update
            </button>
            <div class="loading" id="loading-<?= $tvShowId ?>" style="display:none">Loading...</div>
            <pre id="show-<?= $tvShowId ?>"></pre>
            <?
            echo "<hr/>";
        }
//    exit;
    }
}
$time_end = microtime(true);
var_dump($time_end - $time_start . " s")
?>
</body>
</html>