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

    public function insertLink($id, $season, $episode, $link)
    {
        $this->connect();
        $stmt = $this->dbh->prepare("
            INSERT INTO links (id, season, episode, link)
            VALUES (:id, :season, :episode, :link)
            ON DUPLICATE KEY UPDATE link=:link"
        );
        $res = $stmt->execute(array(
            "id" => $id,
            "season" => $season,
            "episode" => $episode,
            "link" => $link
        ));
        $this->disconnect();
        return $res;
    }

    public function showsInit($shows)
    {
        foreach ($shows as $show) {
            $name = addslashes($show["name"]);
            $quality = isset($show["quality"]) ? $show["quality"] : 720;
            $sql = "INSERT INTO shows (id, name, lastSeenSeason, lastSeenEpisode, quality, status)
                VALUES ($show[id], '$name', $show[lastSeenSeason], $show[lastSeenEpisode], $quality, $show[status])";
            echo $sql . "<br/>";
            $this->executeRaw($sql);
        }
        echo count($shows);
    }

    public function getShows()
    {
        $sql = "SELECT * FROM shows ORDER BY name";
        return $this->selectRaw($sql);
    }

    public function getLinks()
    {
        $sql = "SELECT * FROM links";

        $res = array();
        foreach ($this->selectRaw($sql) as $item) {
            $id = $item["id"];
            $season = $item["season"];
            $episode = $item["episode"];
            $link = $item["link"];
            $res[$id][$season][$episode] = $link;
        }
        return $res;
    }

    public function addShowsLinks($id, $array)
    {
        foreach ($array as $season => $episodes) {
            foreach ($episodes as $episode => $link) {
                $this->insertLink($id, $season, $episode, $link);
            }
        }
    }

    public function removeShowsOldLinks($id, $season, $episode)
    {
        $this->connect();
        $stmt = $this->dbh->prepare("
            DELETE FROM links
            WHERE id = :id AND (season < :season OR (season = :season AND episode <= :episode))"
        );
        $res = $stmt->execute(array(
            "id" => $id,
            "season" => $season,
            "episode" => $episode
        ));
        $this->disconnect();
        return $res;
    }

    public function updateShow($id, $lastSeason, $lastEpisode)
    {
        $this->connect();
        $stmt = $this->dbh->prepare("
            UPDATE shows
            SET lastSeenSeason = :season, lastSeenEpisode = :episode
            WHERE id = :id"
        );
        $res = $stmt->execute(array(
            "id" => $id,
            "season" => $lastSeason,
            "episode" => $lastEpisode
        ));
        $this->disconnect();
        return $res;
    }

    public function getShowsLink($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare("
            SELECT *
            FROM links
            WHERE id = :id"
        );
        $stmt->execute(array("id" => $id));
        $this->disconnect();
        return $this->fetchAllToJSON($stmt->fetchAll(), true);
    }

    private function fetchAllToJSON($array, $html = false)
    {
        foreach ($array as $k => $elt) {
            foreach ($elt as $key => $val) {
                if (is_numeric($key))
                    unset($array[$k][$key]);
                if ($html)
                    $array[$k]["html"] = linksToHTML($array[$k]);
            }
        }
        return json_encode($array);
    }

    public function getShowQuality($id) {
        $this->connect();
        $stmt = $this->dbh->prepare("
            SELECT quality
            FROM shows
            WHERE id = :id"
        );
        $stmt->execute(array("id" => $id));
        $this->disconnect();
        return $stmt->fetch();
    }

    public function addLinks($shows)
    {
        $values = "";
        $count = 0;
        foreach ($shows as $show) {
            $count++;
            $link = addslashes($show["link"]);
            if (isset($show["id"])) {
                $values .= "($show[id], $show[season], $show[episode], '$link')";
                if ($count !== count($shows))
                    $values .= ", ";
            }
        }

        $sql = "INSERT INTO links (id, season, episode, link) VALUES $values";
        echo $sql;
//        $this->insertRaw($sql);
    }

    public function getAllLinks() {
        $this->connect();
        $stmt = $this->dbh->prepare("
        SELECT *
        FROM links l
          LEFT JOIN shows s ON l.id = s.id
        WHERE LENGTH(l.link) > 0");
        $stmt->execute();
        $this->disconnect();
        return $this->fetchAllToJSON($stmt->fetchAll(), true);
    }


}