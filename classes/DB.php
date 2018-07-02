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
        $dbname = 'showchecker';
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
        $sql = "SELECT * FROM shows";
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
        $this->insertRaw($sql);
    }


}