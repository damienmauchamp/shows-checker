<?php
/**
 * Created by PhpStorm.
 * User: dmauchamp
 * Date: 23/05/2018
 * Time: 16:31
 */

namespace TVShowsAPI;

use \TVShowsAPI\APICall as api;

class TVSeason
{

    private $id;
    private $showId;
    private $number;
    private $episodesCount;
    private $airDate;
    private $episodes;

    /**
     * TVSeason constructor.
     * @param $id
     * @param $showId
     * @param $number
     * @param $episodesCount
     * @param $airDate
     */
    public function __construct($id, $showId, $number, $episodesCount, $airDate)
    {
        $this->id = $id;
        $this->showId = $showId;
        $this->number = $number;
        $this->episodesCount = $episodesCount;
        $this->airDate = $airDate;
    }

    public function getEpisodes()
    {
        $url = API_URL . URL_TV_SHOW . $this->showId . "/season/$this->number";
        $response = json_decode(api::get($url));
        $episodes = isset($response->episodes) ? $response->episodes : null;
        return $episodes;
    }

    public function isWatched($season)
    {
        return $season > $this->number;
    }

    public function isAvailable() {
        return $this->airDate < strtotime("now");
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAirDate()
    {
        return $this->airDate;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return mixed
     */
    public function getEpisodesCount()
    {
        return $this->episodesCount;
    }


}