<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 14.11.2018
 * Time: 0:19
 */

namespace chw;


class error
{
    public function __construct($type, $string)
    {
        $this->show(array(
            "type" => $type, "msg" => $string
        ));
    }

    private function show($msg)
    {
        echo json_encode($msg);
    }
}