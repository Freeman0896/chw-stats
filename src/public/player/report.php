<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 18.11.2018
 * Time: 22:24
 */

namespace chw;

const TIME = 3600;
const DVIDER = 10;

class report
{
     private $user; 
     private $db;
     private $reports;

     public function __construct(&$data) {
          $this->user = $data["user"];
          $this->db = new data_base();

          $this->save();
     }

     private function save()
     {
          $result = 'SELECT * FROM `players` WHERE `steam_id` = ' . $this->user . '';
          $row = $this->db->get_db()->query($result);
          $row = $row->fetch_assoc();

          $this->reports = intval($row["reports"]) + 1;

          $query = 'UPDATE `players` SET `reports`= '. $this->reports .' WHERE `steam_id` = ' . $this->user . '';
          $this->db->get_db()->query($query);

          $div1 = intdiv($this->reports, DVIDER);

          if ($this->reports >= 10)
          {
               $bantime = time() + (TIME * 6);

               $query = 'UPDATE `players` SET `reports`= 0, `likes`= ' . $bantime . '  WHERE `steam_id` = ' . $this->user . '';
               $this->db->get_db()->query($query);
          }

          echo json_encode($this->reports);
     }
}
