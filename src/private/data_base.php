<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 14.11.2018
 * Time: 0:11
 */

namespace chw;


class data_base
{
    public $mysql;

    public function __construct()
    {
        $this->mysql = new \mysqli('a', 'b', 'v', "d");

        if ($this->mysql->connect_errno) {
            new error(error_types::DB_ERROR, $this->mysql->connect_errno . " : " . $this->mysql->connect_error); exit;
        }
    }

    public function get_player($steam_id)
    {
        $sql = "SELECT * FROM `players` WHERE `steam_id` = $steam_id";

        if (!$result = $this->mysql->query($sql)) {
            new error(error_types::DB_ERROR, $this->mysql->error);
        }

        if ($result->num_rows === 0) {
            return $this->create_player($steam_id);
        }

        return new player($result->fetch_assoc());
    }

    public function get_db() : \mysqli
    {
        return $this->mysql;
    }

    private function create_player($steam_id)
    {
        $result = player::create($steam_id, $this);

        if ($result == TRUE) { return $this->get_player($steam_id); } else { return null; }
    }
}