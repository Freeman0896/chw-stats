<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 14.11.2018
 * Time: 0:00
 */

namespace chw;

class stats
{
    private $data;
    private $db;
    private $time;

    public function __construct(&$m_data, $type, &$time_params)
    {
        $this->data = $m_data; $this->db = new data_base(); $this->time = $time_params;

        if ($type == data_types::STATS_PRE_GAME) $this->get(); else $this->set();
    }

    public function set()
    {
        $players = $this->data["players"];
        $match_params = $this->data["game"];
        $heroes = $match_params["heroes"];
        $game = $match_params["players"];

        new game($game, $this->time, $this->db);
        new heroes($heroes, $this->db);

        foreach ($players as $player_data)
        {
            $player = $this->db->get_player($player_data["steam_id"]);
            if ($player != NULL) $player->write($this->db, $player_data);
        }
    }

    public function get()
    {
        $data = array();
        $data["players"] = array();

        foreach ($this->data["players"] as $steam_id)
        {
            $player = $this->db->get_player($steam_id);
            if ($player != NULL) array_push($data["players"], get_object_vars($player));
        }

        $data["heroes"] = heroes::get($this->db);

        echo json_encode($data); return;
    }
}