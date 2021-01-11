<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 17.12.2018
 * Time: 16:04
 */

namespace chw;


class serializer
{
    public static final function for_each_player() : void
    {
        if ($_SERVER['REMOTE_ADDR'] == constants::$admin_ip)
        {
            $db = new data_base();

            $players = array();

            $sql = "SELECT * FROM `players` WHERE `games` > 1";

            if (!$result = $db->get_db()->query($sql)) {
                new error(error_types::DB_ERROR, $db->get_db()->error);
            }

            while($row = $result->fetch_assoc()){
                if($row["games"] > 1)
                {
                    $db->get_db()->query("INSERT INTO `items`(`steam_id`, `def_id`, `rarity`, `quality`, `tradeable`, `is_medal`) VALUES  (". $row["steam_id"] .", 316, 10, 4, 1, 1)");
                }
            }
        }
    }
}