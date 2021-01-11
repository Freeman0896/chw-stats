<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 13.11.2018
 * Time: 23:49
 */

namespace chw;

class server_data
{
    public $type;
    public $data;
    public $key;
    public $time;

    static public function create($json)
    {
        return new server_data($json["type"], $json["data"], $json["key"],  $json["time"]);
    }

    public function __construct($m_type, $m_data, $m_key, $m_time)
    {
        $this->type = $m_type; $this->data = $m_data; $this->key = $m_key; $this->time = $m_time;
    }
}
