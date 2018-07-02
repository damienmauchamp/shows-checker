<?php
/**
 * Created by PhpStorm.
 * User: dmauchamp
 * Date: 24/05/2018
 * Time: 11:31
 */

namespace TVShowsAPI;


class TVEpisode
{

    private $id;
    private $showId;
    private $season;
    private $number;
    private $airDate;

    /**
     * TVEpisode constructor.
     * @param $id
     * @param $showId
     * @param $seasonId
     * @param $number
     * @param $airDate
     */
    public function __construct($id, $showId, $season, $number, $airDate)
    {
        $this->id = $id;
        $this->showId = $showId;
        $this->season = $season;
        $this->number = $number;
        $this->airDate = $airDate;
    }

    public function isWatched($season, $episode)
    {
        return !(($season == $this->season && $episode < $this->number) || ($season < $this->season));
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
    public function getShowId()
    {
        return $this->showId;
    }

    /**
     * @return mixed
     */
    public function getSeason()
    {
        return $this->season;
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
    public function getAirDate()
    {
        return $this->airDate;
    }




}