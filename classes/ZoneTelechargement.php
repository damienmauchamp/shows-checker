<?php
/**
 * Created by PhpStorm.
 * User: dmauchamp
 * Date: 13/07/2018
 * Time: 14:34
 */

namespace TVShowsAPI;

use DOMDocument;

class ZoneTelechargement
{
    /**
     * ZoneTelechargement constructor.
     */
    public function __construct()
    {

    }

    public static function search($search, $quality = "720", $lang = "vostfr")
    {
        $domain = "https://zone-telechargement1.ws/";
        $domain = "https://1ww.zones-telechargement1.com/";
//        $searchUrl = "index.php?story=$search&do=search&subaction=search";
//        $res = "hd$quality";
        $searchUrl = "index.php?story=" . urlencode("$search") . "&do=search&subaction=search";
        $recherche = $domain . $searchUrl;
        return $recherche;
//        return self::getResultLinks($recherche, $quality, $lang);
    }

    public static function getResultLinks($html, $quality = 720, $lang = "vostfr")
    {
        $links = array();
        if (!$html) return false;
        $hrefs = self::getLinksFromHtml($html);

        foreach ($hrefs as $node) {
            $link = $node->getAttribute('href');
            if (strpos($link, $quality . "") !== false && strpos($link, $lang) !== false && strpos($link, "anime") == false)
                $links[] = $link;
        }
        return array_unique($links);
    }

    public static function getSeasonLinks($url, $episodes)
    {
        $links = array();
        if (!$html = self::curlGETRequestToHtml($url))
            return false;
        $hrefs = self::getLinksFromHtml($html);

        foreach ($hrefs as $node) {
            $text = $node->nodeValue;
            $link = $node->getAttribute('href');

//            var_dump($episodes);exit;

            foreach ($episodes as $e => $null) {
                if (strpos($text, "Episode $e") !== false) {
                    $links[$e][] = $link;
//                    var_dump("$text : $link");
                }
            }
        }

        return $links;
    }

    public static function getFinalLinks($links, $host = "uptobox")
    {
//        echo "<hr/><hr/>";
        $final = array();
        foreach ($links as $episode => $dlLinks) {
//            var_dump("Episode $episode");
            foreach ($dlLinks as $link) {
//                var_dump($link);
                $hrefs = self::curlDLProtect($link);
                foreach ($hrefs as $node) {
                    $dlLink = $node->getAttribute('href');
                    if (strpos($dlLink, $host) !== false) {
                        $final[$episode]["link"] = $dlLink;
                        $final[$episode]["html"] = "<div class='link'><a href='$dlLink' target='_blank'>$dlLink</div>";
                        break;
                    }
                }
            }
//            echo "<hr/>";
        }
        return $final;
    }

    public static function curlGETRequestToHtml($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $html = curl_exec($ch);

        if ($html) {
            curl_close($ch);
            return $html;
        } else {
            echo "The website could not be reached (Search).";
        }
        return false;
    }

    public static function curlDLProtect($url)
    {
        $liens = array();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => trim($url),
            CURLOPT_USERAGENT => 'Codular Sample cURL Request',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                "submit" => "Continuer"
//                item1 => 'value',
//                item2 => 'value2'
            )
        ));

        $html = curl_exec($curl);
        if ($html) {
            curl_close($curl);
            $liens = self::getLinksFromHtml($html);
        } else {
            echo "The website could not be reached (DLProtect).";
        }
        return $liens;
    }


    private static function getLinksFromHtml($html)
    {
        $hrefs = array();
        if ($html) {
            $dom = new DOMDocument();
            @$dom->recover = true;
            @$dom->strictErrorChecking = false;
            @$dom->loadHTML($html);

            // récupération des liens
            $hrefs = @$dom->getElementsByTagName('a');
        }
        return $hrefs;
    }
}