<?
if (!isset($root)) {
    $root = '../';
}
?>
<title><?= isset($pageTitle) ? $pageTitle : "SC" ?></title>
<script src="<?= $root ?>libs/jquery/jquery-1.12.1.js"></script>
<script src="<?= $root ?>libs/jquery/jquery-migrate-1.2.1.min.js"></script>
<script>


    function getLinks(btn) {
        var id = $(btn).attr("data-show-id");
        var lastSeason = $(btn).attr("data-show-last-season");
        var lastEpisode = $(btn).attr("data-show-last-episode");
//        var quality = $(btn).attr("data-show-quality");
        var loading = $("#loading-" + id);

        loading.show();
        $.ajax({
            url: "./ajax/links.php",
            method: "GET",
            data: {
                id: id,
                lastSeenSeason: lastSeason,
                lastSeenEpisode: lastEpisode
//                quality: quality,
//                seen: 0
            },
            success: function (data) {
                console.log(data);
                $.each(data, function (season, eps) {
                    $.each(eps, function (ep, infos) {
                        $("#show-" + id).empty().append("S" + season + "E" + ep + infos["html"]);
                    });
                });
                loading.hide();
            }, error: function (e) {
                console.log(e);
                loading.hide();
            }
        });
    }

    $(document).ready(function () {
        var seasonsSelects = $(".seasons");
        var episodesSelects = $(".episodes");

        seasonsSelects.on("change", function () {
            var id = $(this).attr("data-show-id");
            var season = $(this).val();
            var nEpisodes = $("option:selected", this).attr("data-season-episodes");
            var selectEpisodes = $("#episodes-" + id);
            var btn = $("#btn-" + id);

            selectEpisodes.empty();
            for (var e = 1; e < nEpisodes; e++) {
                selectEpisodes.append("<option value=\"" + e + "\">" + e + "</option>");
            }

            btn.attr("data-show-last-season", season);
            btn.attr("data-show-last-episode", 1);
        });

        episodesSelects.on("change", function () {
            var id = $(this).attr("data-show-id");
            var episode = $(this).val();
            var btn = $("#btn-" + id);
            btn.attr("data-show-last-episode", episode);
        });
    });

</script>