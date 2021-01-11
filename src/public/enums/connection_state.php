<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 16.11.2018
 * Time: 0:02
 */

namespace chw;


class connection_state
{
    const DOTA_CONNECTION_STATE_UNKNOWN = 0;
    const DOTA_CONNECTION_STATE_NOT_YET_CONNECTED =	1;
    const DOTA_CONNECTION_STATE_CONNECTED =	2;
    const DOTA_CONNECTION_STATE_DISCONNECTED = 3;
    const DOTA_CONNECTION_STATE_ABANDONED = 4;
    const DOTA_CONNECTION_STATE_LOADING = 5;
    const DOTA_CONNECTION_STATE_FAILED	= 6;
}