<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 24/05/2018
 * Time: 19:17
 */

namespace TVShowsAPI;


use http\Exception;
use PDO;

class DB
{

    /**
     * @var PDO null
     */
    private $dbh;

    /**
     * DB constructor.
     */
    public function __construct()
    {
    }

    private function connect()
    {

        $host = 'localhost';
        $dbname = 'shows-checker';
        $env = explode(":", $this->getEnv());
        $username = $env[0] ? $env[0] : null;
        $passwd = $env[1] ? $env[1] : null;

        try {
            $this->dbh = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $username, $passwd);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    private function disconnect()
    {
        $this->dbh = null;
    }

    private function getEnv()
    {
        return file_get_contents(dirname(__DIR__) . '/.env');
    }

    /**
     * @param TVShow $show
     * @return bool
     */
    public function addShow($show)
    {
        $this->connect();

        $sql = "
            INSERT INTO shows (id, name, lastSeenSeason, lastSeenEpisode, quality, status, poster)
            VALUES (:id, :name, :lastSeenSeason, :lastSeenEpisode, :quality, :status, :poster)
            ON DUPLICATE KEY UPDATE name=:name, lastSeenSeason=:lastSeenSeason, lastSeenEpisode=:lastSeenEpisode, quality=:quality, status=:status, poster=:poster";
        $stmt = $this->dbh->prepare($sql);
        $resShow = $stmt->execute(array(
            "id" => $show->getId(),
            "name" => $show->getName(),
            "lastSeenSeason" => $show->getLastSeenSeason(),
            "lastSeenEpisode" => $show->getLastSeenEpisode(),
            "quality" => $show->getQuality(),
            "status" => $show->getStatus(),
            "poster" => $show->getPoster()
        ));
        unset($sql);

        $resSeasons = true;
        foreach ($show->getSeasons() as $season => $episodes) {
            $sql = "INSERT INTO seasons (`show`, season, episodes)
                    VALUES (:id, :season, :episodes)
                    ON DUPLICATE KEY UPDATE season = :season, episodes = :episodes";
            $stmt = $this->dbh->prepare($sql);
            $resEach = $stmt->execute(array(
                "id" => $show->getId(),
                "season" => $season,
                "episodes" => $episodes
            ));
            if (!$resEach) $resSeasons = false;
            unset($sql);
            unset($resEach);
        }

        $this->disconnect();
        return $resShow && $resSeasons;
    }

    public function getShows()
    {
        $this->connect();
        $sql = "
            SELECT *
            FROM shows
            WHERE status = 1
            ORDER BY name";
        $stmt = $this->dbh->query($sql);
        $stmt->execute();

        $shows = array();
        foreach ($stmt->fetchAll() as $entity) {
            $show = TVShow::withArray($entity);
            $shows[] = $show;
            $sql = "
                SELECT *
                FROM seasons
                WHERE `show` = " . $show->getId();
            $stmt = $this->dbh->query($sql);
            $seasons = $stmt->fetchAll();
            $show->setSeasons($seasons, "array");
        }
        $this->disconnect();
        return $shows;
    }

    public function getShow($id)
    {
        $this->connect();
        $sql = "
            SELECT *
            FROM shows
            WHERE id=$id";
        $stmt = $this->dbh->query($sql);
        $stmt->execute();

        $show = TVShow::withArray($stmt->fetch());
        $sql = "
                SELECT *
                FROM seasons
                WHERE `show` = " . $show->getId();
        $stmt = $this->dbh->query($sql);
        $seasons = $stmt->fetchAll();
        $show->setSeasons($seasons, "array");
        $this->disconnect();
        return $show;
    }

    public function addLink($id, $season, $episode, $link)
    {
        $this->connect();
        $sql = "INSERT INTO links (`show`, season, episode, link)
                VALUES (:id, :season, :episode, :link)
                ON DUPLICATE KEY UPDATE season = :season, episode = :episode, link = :link";
        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute(array(
            "id" => $id,
            "season" => $season,
            "episode" => $episode,
            "link" => $link
        ));
        $this->disconnect();
        return $res;
    }

    public function updateShowProgression($id, $season, $episode)
    {
        $this->connect();
        $sql = "UPDATE shows
                SET lastSeenSeason = :season, lastSeenEpisode = :episode
                WHERE id=:id";
        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute(array(
            "id" => $id,
            "season" => $season,
            "episode" => $episode,
        ));
        $this->disconnect();
        return $res;
    }

    public function removeOldLinks($id, $season, $episode)
    {
        $this->connect();
        $sql = "DELETE FROM links
                WHERE `show` = :id AND NOT
                  ((season = :season AND episode >= :episode) OR (season > :season))";
        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute(array(
            "id" => $id,
            "season" => $season,
            "episode" => $episode,
        ));
        $this->disconnect();
        return $res;
    }

    private function insertRaw($sql)
    {
        $this->connect();
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $this->disconnect();
    }

    private function selectRaw($sql)
    {
        $this->connect();
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $res = array();
        $tmp = array();
        foreach ($stmt->fetchAll() as $item) {
            foreach ($item as $key => $value) {
                if (!is_numeric($key))
                    $tmp[$key] = $value;
            }
            $res[] = $tmp;
            unset($tmp);
        }
        $this->disconnect();
        return $res;
    }

    private function executeRaw($sql)
    {
        $this->connect();
        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute();
        $this->disconnect();
        return $res;
    }


}