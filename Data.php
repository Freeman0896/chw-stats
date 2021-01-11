<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 13.11.2018
 * Time: 23:36
 */

namespace chw;

use chw;

error_reporting(E_ALL); // Error engine - always TRUE!
ini_set('ignore_repeated_errors', TRUE); // always TRUE
ini_set('display_errors', FALSE); // Error display - FALSE only in production environment or real server
ini_set('log_errors', FALSE); // Error logging engine
ini_set('error_log', '/var/www/www-root/data/www/chw-stats'); // Logging file path
ini_set('log_errors_max_len', 1024); // Logging file size

require_once("src/public/data.php");

function OnLoad(){
   new chw\data();
} OnLoad();

