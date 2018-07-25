<?php
/**
 * Created by PhpStorm.
 * User: dmauchamp
 * Date: 23/05/2018
 * Time: 15:40
 */

namespace TVShowsAPI;

//use \TVShowsAPI\APICall as api;

class TVShow
{

    private $id;
    private $name;
    private $lastSeenSeason;
    private $lastSeenEpisode;
    private $quality;
    private $status;
    private $poster;

    private $seasons;


    /**
     * TVShow constructor.
     */
    public function __construct()
    {
        $this->lastSeenSeason = 1;
        $this->lastSeenEpisode = 1;
    }

    public static function withArray($array)
    {
        $instance = new self();
        $instance->fill($array);
        return $instance;
    }

    /**
     * @return bool
     */
    public function isOn()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isOff()
    {
        return !$this->status;
    }

    private function fill($array)
    {
        foreach ($array as $var => $val) {
            if (!is_numeric($var))
                $this->$var = $val;
        }
    }

    public function addToDatabase()
    {
        $db = new db;
        return $db->addShow($this);
    }

    private function progressionToString()
    {
        $lastSeenSeason = $this->getLastSeenSeason();
//        var_dump($lastSeenSeason);exit;

        // SEASONS
        $str = "
            <label for='seasons-" . $this->getId() . "'></label>
            <select class='seasons' data-show-id='" . $this->getId() . "' id='seasons-" . $this->getId() . "'>";
        foreach ($this->seasons as $season => $e) {
            $selected = $season == $this->getLastSeenSeason() ? "selected" : "";
            $str .= "
                <option data-season-episodes='" . $e . "' value='" . $season . "' " . $selected . ">" . $season . "</option>
            ";
        }
        $str .= "
            </select>
        ";

        // EPISODES
        $str .= "
            <label for='episodes-" . $this->getId() . "'></label>
            <select class='episodes' data-show-id='" . $this->getId() . "' id='episodes-" . $this->getId() . "'>";
        for ($e = 1; $e <= $this->seasons->$lastSeenSeason; $e++) {
            $selected = $e == $this->getLastSeenEpisode() ? "selected" : "";
            $str .= "
                <option value='" . $e . "' " . $selected . ">" . $e . "</option>
            ";
        }
        $str .= "
            </select>
        ";

        // BTN Update
        $str .= "
        <button class='btn-show' id='btn-" . $this->getId() . "'
                    data-show-id='" . $this->getId() . "'
                    data-show-last-season='" . $this->getLastSeenSeason() . "'
                    data-show-last-episode='" . $this->getLastSeenEpisode() . "'
                    data-show-quality='" . $this->getQuality() . "'
                    onclick='getLinks(this)'
                    value=''>Update</button>
        ";
        return $str;
    }

    public function toString()
    {
        $str = "
            <div class='show' data-show-id='" . $this->getId() . "'>
                <img class='poster' src='" . $this->getPoster(true) . "' style='width:50px'/>
                <h3 class='title'>" . $this->getName() . "</h3>";
        $str .= $this->progressionToString();
        $str .= "<!--h4 class=''>S" . $this->getLastSeenSeason(true) . "E" . $this->getLastSeenEpisode(true) . "</h4-->
            </div>
            <div class='loading' id='loading-" . $this->getId() . "' style='display:none'>Loading...</div>
            <pre id='show-" . $this->getId() . "'>";
        foreach ($this->getDBLinks() as $links) {
            $str .= "S" . $links["season"] . "E" . $links["episode"] . " <div class='link'><a href='" . $links["link"] . "' target='_blank'>" . $links["link"] . "</a></div>";
        }
        $str .= "</pre>";

        return $str;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param bool $full
     * @return mixed
     */
    public function getLastSeenSeason($full = false)
    {
        return $full ? sprintf("%02d", $this->lastSeenSeason) : $this->lastSeenSeason;
    }

    /**
     * @param mixed $lastSeenSeason
     */
    public function setLastSeenSeason($lastSeenSeason)
    {
        $this->lastSeenSeason = $lastSeenSeason;
    }

    /**
     * @param bool $full
     * @return mixed
     */
    public function getLastSeenEpisode($full = false)
    {
        return $full ? sprintf("%02d", $this->lastSeenEpisode) : $this->lastSeenEpisode;
    }

    /**
     * @param mixed $lastSeenEpisode
     */
    public function setLastSeenEpisode($lastSeenEpisode)
    {
        $this->lastSeenEpisode = $lastSeenEpisode;
    }

    /**
     * @return mixed
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param mixed $quality
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param bool $full
     * @return mixed
     */
    public function getPoster($full = false)
    {
        return $this->poster ? ($full ? "//image.tmdb.org/t/p/w600_and_h900_bestv2" . $this->poster : $this->poster) : null;
    }

    /**
     * @param mixed $poster
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;
    }

    /**
     * @return mixed
     */
    public function getSeasons()
    {
        return $this->seasons;
    }

    /**
     * @param mixed $seasons
     * @param mixed $type
     */
    public function setSeasons($seasons, $type = null)
    {
        if ($type == "array") {
            $this->seasons = (object)array();
            foreach ($seasons as $s) {
                $this->seasons->$s["season"] = intval($s["episodes"]);
            }
        } else {
            $this->seasons = $seasons;
        }
    }

    public function getNumberOfSeasons()
    {
        return count($this->seasons);
    }

    public function getNeededEpisodes($lastSeason = false, $lastEpisode = false)
    {
        $episodes = array();
        $lastSeenSeason = $lastSeason ? $lastSeason : $this->getLastSeenSeason();
        $lastSeenEpisode = $lastEpisode ? $lastEpisode : $this->getLastSeenEpisode();
        foreach ($this->getSeasons() as $season => $numberOfEpisodes) {
            if ($season >= $this->getLastSeenSeason()) {
                for ($e = 1; $e <= $numberOfEpisodes; $e++) {
                    if (($season === $lastSeenSeason && $e >= $lastSeenEpisode)
                        || ($season > $lastSeenSeason)) {
                        $episodes[$season][$e] = null;
                    }
                }
            }
        }
        return $episodes;
    }

    public function updateLinks($links)
    {
        $res = true;
        $db = new db;
        foreach ($links as $season => $episodes) {
            foreach ($episodes as $episode => $e) {
                if (!$db->addLink($this->getId(), $season, $episode, $e["link"]))
                    $res = false;
            }
        }
        return $res;
    }

    public function updateProgression($season, $episode)
    {
        $db = new db;
        $this->lastSeenSeason = $season;
        $this->lastSeenEpisode = $episode;
        return $db->updateShowProgression($this->getId(), $season, $episode);
    }

    public function removeOldLinks()
    {
        $db = new db;
        return $db->removeOldLinks($this->getId(), $this->getLastSeenSeason(), $this->getLastSeenEpisode());
    }

    public function getDBLinks()
    {
        $db = new db;
        return $db->getShowLinks($this->getId(), $this->getLastSeenSeason(), $this->getLastSeenEpisode());
    }

    public function getLastSeason()
    {
        end($this->seasons);
        return key($this->seasons);
    }

    public function getLastSeasonEpisode($season)
    {
        return $this->seasons->$season;
    }

    public function upToDate()
    {
        $lastSeason = $this->getLastSeason();
        $lastEpisode = $this->getLastSeasonEpisode($lastSeason);
        return ($this->lastSeenSeason == $lastSeason && $this->lastSeenEpisode == $lastEpisode) || $lastEpisode == 0;
    }


}