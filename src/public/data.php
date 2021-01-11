<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 13.11.2018
 * Time: 23:39
 */

namespace chw;

////public
require_once("server_data.php");
require_once("stats/stats.php");
require_once("structs/error.php");
require_once("player/player.php");
require_once("player/report.php");
require_once("player/inventory.php");
require_once("player/item.php");
require_once("player/trade.php");
require_once("enums/error_types.php");
require_once("enums/data_types.php");
require_once("enums/connection_state.php");
require_once("game/game.php");
require_once("game/heroes.php");

///private
require_once($_SERVER['DOCUMENT_ROOT'] . "/src/private/constants.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/src/private/data_base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/src/private/utils.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/src/tools/serializer.php");


class data
{
    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') { $this->get(); return; }

        $data = server_data::create(json_decode($_POST["payload"], true));

        $this->set($data);
    }

    private function set(server_data &$data)
    {
        if ($data->type == data_types::STATS_PRE_GAME) {new stats($data->data, $data->type, $data->time); return;}
        if ($data->type == data_types::INVENTORY && $data->key != NULL) {$inv = new inventory(); $inv->get_all($data->data); return;}

        if ($data->key == NULL){ $this->client($data->data,$data->type); return; }
        if (!constants::validate($data->key)) return;

        switch ($data->type)
        {
            case data_types::GET:
                return;
            case data_types::STATS_POST_GAME:
                new stats($data->data, $data->type, $data->time); break;
            case data_types::PLUS_STATS:
                heroes::get_plus_stats($data->data); break;
            case data_types::ITEM:
                $item = new item(); $item->create($data->data); break;
            case data_types::DELETE_ITEM:
                $item = new item(); $item->delete($data->data); break;
            case data_types::TRADE:
                new trade($data->data); break;
            case data_types::REPORT:
                new report($data->data); break;
        }
    }

    private function client(&$data, &$type)
    {
        switch ($type)
        {
            case data_types::GET:
                return;
            case data_types::GAME:
                game::get_last($data); break;
            case data_types::INVENTORY:
                $inv = new inventory(); $inv->get($data); break;
            case data_types::ITEM:
                $item = new item(); $item->set($data); break;
            case data_types::PLUS_STATS:
                heroes::get_plus_stats($data); break;
        }
    }

    private function get()
    {
        new error(error_types::RQS_ERROR, "Its GET request!");
    }
}
