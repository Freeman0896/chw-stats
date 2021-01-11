<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 13.11.2018
 * Time: 23:38
 */

namespace chw;


class player
{
    const GAMES_CALIBRATE = 10;

    public $steam_id;
    public $name;
    public $games;
    public $wins;
    public $reports;
    public $likes;
    public $gold;
    public $kills;
    public $deaths;
    public $assists;
    public $last_hits;
    public $denies;
    public $gold_per_min;
    public $xp_per_minute;
    public $gold_spent;
    public $misses;
    public $net_worth;
    public $hero_damage;
    public $tower_damage;
    public $hero_healing;
    public $hero_pick_order;
    public $is_using_plus;
    public $plus_expire;
    public $calibrated;
    public $rating;
    public $status;
    public $displayed_medal;
    public $prestige;
    public $shards;

    public function __construct($data) {
        foreach($data as $var => $value) {
            $this->$var = $value;
        }
    }

    public function save($db)
    {

    }

    public function write(data_base &$db, $data)
    {
        $is_won = $data["win_state"];

        $this->games++; if ($is_won === TRUE) { $this->wins++; }
        $this->gold += $data["gold"];
        $this->kills += $data["kills"];
        $this->deaths += $data["deaths"];
        $this->assists += $data["assists"];
        $this->last_hits += $data["last_hits"];

        if ($this->games > 2)
        {
            $this->gold_per_min = (($this->gold_per_min / ($this->games - 1)) + $data["gold_per_min"]) / $this->games;
            $this->xp_per_minute = (($this->xp_per_minute / ($this->games - 1)) + $data["xp_per_minute"]) / $this->games;
            $this->net_worth = (($this->net_worth / ($this->games - 1)) + $data["net_worth"]) / $this->games;
        }

        $this->gold_spent += $data["gold_spent"];
        $this->hero_damage += $data["hero_damage"];
        $this->tower_damage += $data["tower_damage"];
        $this->hero_healing += $data["hero_healing"];
        $this->hero_pick_order = round(($this->games + 1) / 106, 2);

        if($this->is_using_plus == 1) $this->plus_status_verify($db);

        $this->update_game_rating($db, $data);

        $db->get_db()->query("UPDATE `players` SET `games`= ". $this->games .",`wins`= ". $this->wins .",`gold`= ". $this->gold .",`kills`= ". $this->kills .",`deaths`= ". $this->deaths .",`assists`= ".$this->assists.",`last_hits`= ". $this->last_hits .",`denies`= ". $this->denies .",`gold_per_min`= ". $this->gold_per_min .",`xp_per_minute`= ". $this->xp_per_minute .",`gold_spent`= ". $this->gold_spent .",`misses`= ". $this->misses .",`net_worth`= ". $this->net_worth .",`hero_damage`= ". $this->hero_damage .",`tower_damage`= ". $this->tower_damage .",`hero_healing`= ". $this->hero_healing .",`hero_pick_order`= ". $this->hero_pick_order ." WHERE `steam_id` = ". $this->steam_id ."");
    }

    private function calibrate(&$data)
    {

    }

    private function update_game_rating(data_base &$db, &$data)
    {
        $rating = $this->rating;

        $kills = $data['kills'];
        $deaths = $data['deaths'];
        $assists = $data['assists'];

        $win_state = $data['win_state'];
        $connection_state = $data['connection_state'];

        $gpm = $data['gold_per_min'];

        $stuns = $data['stuns'] <= 0 ? 1 : $data['stuns'];

        $total_heal = $data['hero_healing'];
        $tower_damage = $data['tower_damage'];
        $total_damage_done_to_heroes = $data['hero_damage'];
        $deni = $data['denies'];
        $total_damage_taken_from_heroes = $data['total_damage_taken_from_heroes'];
        $last_hits = $data['last_hits'];
        $level = $data['level'];
        $total_gold = $data['gold'];
        $exp_per_min = $data['xp_per_minute'];
        $game_time = $data['time'] <= 570 ? 570 : $data['time'];

        if ($win_state == TRUE) { $rating += 35; } else { $rating -= 45; }

        $rating = $rating + ($kills + $assists) * 1800 / $game_time;
        $rating = $rating + ($last_hits + $deni * 10) * 200 / $game_time;
        $rating = $rating + ($last_hits + $deni * 10) * 200 / $game_time;
        $rating = $rating + $stuns * 300 / $game_time;
        $rating = $rating - ($deaths * 3500) / $game_time;
        if ($connection_state == connection_state::DOTA_CONNECTION_STATE_ABANDONED){ $rating = $rating - 25; }

        $this->rating = $rating;

        if ($this->games == self::GAMES_CALIBRATE)
        {
            $this->calibrated = 1; $this->rating = $this->rating * 4;
        }

        if ($this->prestige <= 10 && $this->rating >= 10000)
        {
            $this->prestige++;
            $this->rating = 1;
        }

        if($this->rating > 10000) $this->rating = 10000;
        if($this->rating <= 0) $this->rating = 1;

        $db->get_db()->query("UPDATE `players` SET `rating`= ". $this->rating .",`prestige`= ". $this->prestige .", `calibrated` = ". $this->calibrated ." WHERE `steam_id` = ". $this->steam_id ."");
    }

    private function plus_status_verify(data_base &$db)
    {
        $unix = time();

        if ($this->plus_expire <= $unix)
        {
            $db->get_db()->query("UPDATE `players` SET `is_using_plus`= 0, `plus_expire`= 0 WHERE `steam_id` = ". $this->steam_id ."");
        }

    }
}
