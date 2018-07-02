<?php
/**
 * Created by PhpStorm.
 * User: dmauchamp
 * Date: 23/05/2018
 * Time: 15:40
 */

namespace TVShowsAPI;

use \TVShowsAPI\APICall as api;

class TVShow
{

    private $id;
    private $lastSeenSeason;
    private $lastSeenEpisode;
    private $status;

    private $name;
    private $numberOfSeasons;
    private $seasons;

    /**
     * TVShow constructor.
     * @param $id
     * @param int $lastSeenSeason
     * @param int $lastSeenEpisode
     * @param bool $status
     */
    public function __construct($id, $lastSeenSeason = 1, $lastSeenEpisode = 1, $status = true)
    {
        $this->id = $id;
        $this->lastSeenSeason = $lastSeenSeason;
        $this->lastSeenEpisode = $lastSeenEpisode;
        $this->status = $status;
        $this->fetchSeasons();
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

    private function fetchSeasons()
    {
        $url = API_URL . URL_TV_SHOW . $this->id;
        $response = json_decode(api::get($url));

        $this->numberOfSeasons = isset($response->number_of_seasons) ? $response->number_of_seasons : 0;
        $this->seasons = isset($response->seasons) ? $response->seasons : null;
        $this->name = isset($response->original_name) ? $response->original_name : null;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getLastSeenSeason()
    {
        return $this->lastSeenSeason;
    }

    /**
     * @return int
     */
    public function getLastSeenEpisode()
    {
        return $this->lastSeenEpisode;
    }

    /**
     * @return mixed
     */
    public function getNumberOfSeasons()
    {
        return $this->numberOfSeasons;
    }

    /**
     * @return mixed
     */
    public function getSeasons()
    {
        return $this->seasons;
    }

//    /**
//     * @param $id
//     * @return TVShow
//     */
//    public static function withID($id)
//    {
//        $instance = new self();
//        $instance->id = $id;
////        $instance->FONCTION();
//        return $instance;
//    }


    public function getTvShow($id)
    {
        $url = API_URL . URL_TV_SHOW . $id;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url . "?api_key=" . API_KEY
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


}