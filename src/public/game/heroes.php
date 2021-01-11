<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 16.11.2018
 * Time: 13:30
 */

namespace chw;


class heroes
{
    private $data;
    private $db;

    public function __construct(&$heroes, data_base &$db)
    {
        if ($heroes !== NULL) { $this->data = $heroes; $this->db = $db; $this->set(); }
    }

    private function set()
    {
        foreach ($this->data as $hero)
        {
            $hero_name = $hero["name"];
            $id = $hero["id"];

            $last_hits = $hero["last_hits"];
            $net_worth = $hero["net_worth"];
            $deaths = $hero["deaths"];
            $level = $hero["level"];
            $kills = $hero["kills"];
            $picks = 1;
            $games = 1;
            $wins = 0;

            if ($hero["is_won"] === TRUE) $wins = 1;

            $sql = "SELECT * FROM `heroes` WHERE `hero_id` = '". $id ."'";

            if (!$result = $this->db->get_db()->query($sql)) {
                new error(error_types::DB_ERROR, $this->db->get_db()->error);
            }

            if ($result->num_rows === 0) {
                 heroes::create($id, $hero_name, $this->db);
            }
            else
            {
                $data = $result->fetch_assoc();

                $last_hits = (($data["last_hits"] / $picks) + $last_hits) / ($picks + 1);
                $net_worth = (($data["net_worth"] / $picks) + $net_worth) / ($picks + 1);
                $deaths = (($data["deaths"] / $picks) + $deaths) / ($picks + 1);
                $level = (($data["level"] / $picks) + $level) / ($picks + 1);
                $kills = (($data["kills"] / $picks) + $kills) / ($picks + 1);

                $picks = $data["picks"] + 1;
                $games = $data["games"] + 1;
                $wins += $data["wins"];
            }

            $items = $hero["items"];

            foreach ($items as $item)
            {
                $col = $this->db->get_db()->query("SELECT ". $item ." FROM `heroes` WHERE `hero_id` = ". $id ."");

                if (!$col)
                {
                    $this->db->get_db()->query("ALTER TABLE `heroes` ADD `". $item ."` INT NOT NULL DEFAULT '0' AFTER `net_worth`");
                }
                else
                {
                    $value = $col->fetch_assoc()[$item] + 1;
                    $this->db->get_db()->query("UPDATE `heroes` SET `". $item ."` = ". $value ." WHERE `hero_id` = ". $id ."");
                }
            }

            $this->db->get_db()->query("UPDATE `heroes` SET `picks`= ". $picks .",`wins`= ". $wins .",`games`= ". $games .",`deaths`= ". $deaths ." ,`kills`= ". $kills .",`last_hits`= ". $last_hits .",`level`= ". $level .",`net_worth`= ". $net_worth ." WHERE `hero_id` = ". $id ."");
        }
    }

    public static function get(data_base &$db) : array
    {
        $heroes = array();

        $sql = "SELECT `hero_name`, `picks`, `wins` FROM `heroes` ORDER BY `heroes`.`picks` DESC";

        if (!$result = $db->get_db()->query($sql)) {
            new error(error_types::DB_ERROR, $db->get_db()->error);
        }

        while($row = $result->fetch_assoc()){ $heroes[$row["hero_name"]] = array("picks" => $row["picks"], "wins" => $row["wins"]); }

        return $heroes;
    }

    public static function get_plus_stats(&$data)
    {
        $db = new data_base();

        $sql = 'SELECT * FROM `heroes` WHERE `hero_name` = "'. $data .'"';

        if (!$result = $db->get_db()->query($sql)) {
            new error(error_types::DB_ERROR, $db->get_db()->error);
        }

        $hero = $result->fetch_assoc();

        $hero["total_games"] = game::get_total_games($db);

        echo json_encode($hero);
    }

    private static function create($id, $name, data_base &$db) : bool
    {
        $sql = "INSERT INTO heroes (`hero_id`, `hero_name`) VALUES ('" . $id . "', '". $name ."')";

        return $db->get_db()->query($sql);
    }
}