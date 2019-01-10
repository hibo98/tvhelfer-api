<?php

require_once "connect.php";
require_once "class.programm_dab.php";

class DABProgrammpaket {

    public static $all = array();
    public $id;
    public $name;
    public $status;
    public $programms = array(); //Array of DABProgramm (Class)
    public $parent; //Class DABProgrammpaket

    private function __construct($PaketDB) {
        $this->id = (int) $PaketDB['id'];
        $this->name = $PaketDB['name'];
        $this->status = $PaketDB['status'];
        $this->parent = DABProgrammpaket::getPaket($PaketDB['parent']);
        $this->pushProgramms();
        if (!empty($this->parent)) {
            $this->programms = array_merge($this->programms, $this->parent->programms);
            usort($this->programms, array($this, "sortCompare"));
        }
    }
    
    private function sortCompare($a, $b) {
        return strcasecmp($a->name, $b->name);
    }

    private function pushProgramms() {
        global $DB;
        $programms = $DB->query("SELECT * FROM dabProgramms WHERE programmpaketId = ? ORDER BY id", $this->id);
        foreach ($programms as $programm) {
            $this->programms[$programm['id']] = new DABProgramm($programm);
        }
    }
    
    public static function getPakets() {
        $result = array();
        global $DB;
        $answer = $DB->query("SELECT * FROM dabProgrammpaket");
        foreach ($answer as $paket) {
            array_push($result, new DABProgrammpaket($paket));
        }
        return $result;
    }

    public static function getPaket($id) {
        if (empty($id)) {
            return null;
        }
        if (!array_key_exists($id, DABProgrammpaket::$all)) {
            global $DB;
            $paket = $DB->query("SELECT * FROM dabProgrammpaket WHERE id = ?", $id)[0];
            DABProgrammpaket::$all[$id] = new DABProgrammpaket($paket);
        }
        return DABProgrammpaket::$all[$id];
    }
    
    public static function getPaketsByLoc($id) {
        $result = array();
        global $DB;
        $answer = $DB->query("SELECT dabTransmitters.programmpaketId FROM dabTransmitters LEFT JOIN dabProgrammpaket ON dabProgrammpaket.id = dabTransmitters.programmpaketId WHERE dabTransmitters.locationId = ? GROUP BY dabTransmitters.programmpaketId ORDER BY dabProgrammpaket.name", $id);
        foreach ($answer as $paket) {
            array_push($result, DABProgrammpaket::getPaket($paket['programmpaketId']));
        }
        return $result;
    }
}
