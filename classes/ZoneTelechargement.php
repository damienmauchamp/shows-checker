<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 23/05/2018
 * Time: 18:54
 */

namespace TVShowsAPI;


use DOMDocument;

class ZoneTelechargement
{
    private $domain = "https://1ww.zone-telechargement1.com/";

    /**
     * ZoneTelechargement constructor.
     */
    public function __construct()
    {
    }

    public static function search($search, $quality = "720", $lang = "vostfr")
    {
        $domain = "https://1ww.zone-telechargement1.com/";
//        $searchUrl = "index.php?story=$search&do=search&subaction=search";
//        $res = "hd$quality";
        $searchUrl = "index.php?story=" . urlencode("$search") . "&do=search&subaction=search";
        $recherche = $domain . $searchUrl;
        return self::getResultLinks($recherche, $quality, $lang);
    }

    /**
     * @param string $name
     * @param TVEpisode $episode
     * @param string $quality
     * @return string
     */
    public static function getTvEpisodeLink($name, $episode, $quality = "720")
    {
        $link = null;
        $saison = $episode->getSeason();
        $episodes = array($episode->getNumber());

        $links = self::search("$name saison $saison", $quality);


//        $lien = isset($links[0]) ? $links[0] : null;

        if (isset($links[0])) {
            $dlProtectLinks = self::getEpisodesLinks($links[0], $episodes);
            $link = self::getFinalLink($dlProtectLinks, "1fichier");
        }
        return $link;
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

    /**
     * @param $url
     * @param string $quality
     * @param $lang
     * @return array
     */
    private static function getResultLinks($url, $quality, $lang)
    {
        $links = array();
        $html = self::curlGETRequestToHtml($url);
        $hrefs = self::getLinksFromHtml($html);

        foreach ($hrefs as $node) {
            $link = $node->getAttribute('href');
            if (strpos($link, $quality."") !== false && strpos($link, $lang) !== false && strpos($link, "anime") == false)
                $links[] = $link;
        }
        return array_unique($links);
    }

    public static function getEpisodesLinks($url, $episodes)
    {
        $links = array();
        $html = self::curlGETRequestToHtml($url);
        $hrefs = self::getLinksFromHtml($html);

        foreach ($hrefs as $node) {
            $text = $node->nodeValue;
            $link = $node->getAttribute('href');

//            var_dump($episodes);exit;

            foreach ($episodes as $e) {
                if (strpos($text, "Episode $e") !== false) {
                    $links[] = $link;
//                    var_dump("$text : $link");
                }
            }
        }

        return $links;
    }

    public static function getFinalLink($links, $host = "1fichier")
    {
        $final = "";
        foreach ($links as $dlLink) {
            $hrefs = self::curlDLProtect($dlLink);
            foreach ($hrefs as $node) {
                $text = $node->nodeValue;
                $link = $node->getAttribute('href');

                if (strpos($text, $host) !== false) {
                    $final = $link;
                }
            }
        }
        return $final;
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
            echo "The website could not be reached (1).";
        }
        return $liens;
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
            echo "The website could not be reached (2).";
        }
        return false;
    }


}