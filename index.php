<?php
set_time_limit(0);
require_once "start.php";
if (!isset($root)) {
    $root = '';
} ?>
    <title><?= isset($pageTitle) ? $pageTitle : "SC" ?></title>
    <script src="<?= $root ?>libs/jquery/jquery-1.12.1.js"></script>
    <script src="<?= $root ?>libs/jquery/jquery-migrate-1.2.1.min.js"></script>
    <script>
        function getLinks(btn) {
            var id = $(btn).attr("data-show-id");
            var lastSeason = $(btn).attr("data-show-last-season");
            var lastEpisode = $(btn).attr("data-show-last-episode");
            var quality = $(btn).attr("data-show-quality");

            $.ajax({
                url: "./ajax/getLinks.php",
                method: "GET",
                data: {
                    id: id,
                    lastSeason: lastSeason,
                    lastEpisode: lastEpisode,
                    quality: quality
                },
                success: function (data) {
                    $("#show-"+id).empty().append(data);
                }
            });

            console.log(id, lastSeason, lastEpisode, quality);
        }

        $(document).ready(function () {


        });
    </script>
<?

use \TVShowsAPI\ZoneTelechargement as zt;

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
        $tvShowQuality = isset($s["quality"]) ? $s["quality"] : 720;

        // SEASONS
        foreach ($tvSeasons as $tvSeason) {
            $season = new \TVShowsAPI\TVSeason($tvSeason->id, $tvShowId, $tvSeason->season_number, $tvSeason->episode_count, strtotime($tvSeason->air_date));
            $seasonNumber = $season->getNumber();

            if (!$season->isWatched($myLastSeenSeason) && $season->isAvailable()) {
                $seasonEpisodes = $season->getEpisodes();
                var_dump($tvShowName);
                var_dump($season);
                echo "<hr/>";
                ?>
                <button class="btn-show"
                        data-show-id="<?= $tvShowId ?>"
                        data-show-last-season="<?= $myLastSeenSeason ?>"
                        data-show-last-episode="<?= $myLastSeenEpisode ?>"
                        data-show-quality="<?= $tvShowQuality ?>"
                        onclick="getLinks(this)"
                        value="<?= $tvShowName ?>">Update <?= $tvShowName ?></button>
                <pre id="show-<?= $tvShowId ?>">

                </pre>
                <?

                // ÉPISODES
//                foreach ($seasonEpisodes as $tvEpisode) {
//                    $episode = new \TVShowsAPI\TVEpisode($tvEpisode->id, $tvShowId, $season->getNumber(), $tvEpisode->episode_number, strtotime($tvEpisode->air_date));
//
//                    $episodeAirDate = $episode->getAirDate();
//                    $episodeNumber = $episode->getNumber();
//
//                    if (!$episode->isWatched($myLastSeenSeason, $myLastSeenEpisode) && $episode->isAvailable()) {
//                        if (!isset($baseLinks[$tvShowId][$seasonNumber][$episodeNumber]) || $baseLinks[$tvShowId][$seasonNumber][$episodeNumber] == null) {
//                            $tvLinks[$tvShowId][$seasonNumber][$episodeNumber] = zt::getTvEpisodeLink($tvShowName, $episode, $tvShowQuality);
////                            var_dump($baseLinks[$tvShowId][$seasonNumber][$episodeNumber]);
//                            var_dump("aucun lien pour S" . sprintf("%02d", $seasonNumber) . "E" . sprintf("%02d", $episodeNumber));
////                        exit;
//                        } else {
//                            var_dump("OK S" . sprintf("%02d", $seasonNumber) . "E" . sprintf("%02d", $episodeNumber));
//                        }
//                    }
//                }
            }

        }
//        var_dump($tvShow);
    }
}
var_dump($tvLinks);