<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 15.11.2018
 * Time: 23:59
 */

namespace chw;


class game
{
    private $data;
    private $time;
    private $db;

    const MAX_PLAYER_COUNT = 9;

    public function __construct(&$m_data, &$time, data_base &$db)
    {
        $this->data = $m_data; $this->time = $time; $this->db = $db; $this->save();
    }

    private function save()
    {
        $server_time = $this->time["server_system_date_time"];
        $map = $this->time["map"];
        $winner = $this->time["winner"];
        $game_time = $this->time["dota_time"];
        $version = $this->time["version"];

        $statement = "INSERT INTO `games`(`server_version`, `map`, `time`, `dota_time`, `winner`) VALUES ('". $version ."', '". $map ."', '". $server_time ."', ". strval(floor($game_time)) .", ". intval($winner) .")";
        $result = $this->db->get_db()->query($statement);

        if ($result)
        {
            $id = $this->db->get_db()->insert_id;

            for($i = 0; $i <= count($this->data); $i++)
            {
                if ($i <= self::MAX_PLAYER_COUNT)
                {
                    $player_column = "player" . $i;
                    $player_id = $this->data[$i]["steam_id"];
                    $playerData = "player" . $i . "_Data";
                    $player_items = json_encode($this->data[$i]["player_data"]);

                    $this->db->get_db()->query("UPDATE `games` SET `". $player_column ."`= ". intval($player_id) .",`". $playerData ."`= '". $player_items ."' WHERE `game_id` = ". $id ."");
                }
            }
        }
    }

    public static function get_last(&$steam_id)
    {
        $id = $steam_id["id"];
        $db = new data_base();

        $sql = "SELECT * FROM `games` WHERE `player0` = ". $id ." OR `player1` = ". $id ." OR `player2` = ". $id ." OR `player3` = ". $id ." OR `player4` = ". $id ." OR `player5` = ". $id ." OR `player6` = ". $id ." OR `player7` = ". $id ." OR `player8` = ". $id ." OR `player9` = ". $id ."  ORDER BY `games`.`game_id` DESC";

        if (!$result = $db->get_db()->query($sql)) {
            new error(error_types::DB_ERROR, $db->get_db()->error);
        }

        echo json_encode($result->fetch_assoc());
    }

    public static function get_total_games(data_base &$db)
    {
        $query = "SELECT COUNT(*) FROM games";

        $result = $db->get_db()->query($query);

        $rows = $result->fetch_row();

        return $rows[0];
    }
}