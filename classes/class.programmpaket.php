<?php

require_once "connect.php";
require_once "class.programm.php";

class Programmpaket {

    private static $all = array();
    public $id;
    public $name;
    public $status;
    public $FEC;
    public $FFT;
    public $guardintervall;
    public $modulation;
    public $bandwidth;
    public $parent; //Class Programmpaket
    public $programms; //Array of Programms (Class)
    public $dvbt2;

    private function __construct($ProgrammpaketDB, $dvbt2 = false) {
        $this->id = (int) $ProgrammpaketDB['id'];
        $this->name = $ProgrammpaketDB['contraction'];
        $this->status = $ProgrammpaketDB['status'];
        $this->FEC = $ProgrammpaketDB['FEC'];
        $this->FFT = $ProgrammpaketDB['FFT'];
        $this->guardintervall = $ProgrammpaketDB['guardintervall'];
        $this->modulation = $ProgrammpaketDB['modulation'];
        $this->bandwidth = $ProgrammpaketDB['bandwidth'];
        $this->parent = Programmpaket::getPaket($ProgrammpaketDB['parent'], $dvbt2);
        $this->dvbt2 = $dvbt2;
        $this->programms = array();
        $this->pushProgramms();
        //if (!empty($this->parent)) {
        //    $this->programms = array_merge($this->programms, $this->parent->programms);
        //}
        Programmpaket::$all[($this->dvbt2 ? "dvbt2_" : "dvbt_").$this->id] = $this;
    }

    private function pushProgramms() {
        $table = $this->dvbt2 ? "dvbt2_programms" : "dvbt_programms";
        global $DB;
        $programms = $DB->query("SELECT * FROM $table WHERE programmpaketId = ? ORDER BY id", $this->id);
        foreach ($programms as $programm) {
            $this->programms[$programm['id']] = new Programm($programm);
        }
    }
    
    public static function getPakets($dvbt2 = false) {
        $result = array();
        $prefix = $dvbt2 ? "dvbt2_" : "dvbt_";
        global $DB;
        $answer = $DB->query("SELECT * FROM {$prefix}programmpaket");
        foreach ($answer as $paket) {
            array_push($result, new Programmpaket($paket, $dvbt2));
        }
        return $result;
    }

    public static function getPaket($id, $dvbt2 = false) {
        if (empty($id)) {
            return null;
        }
        $prefix = $dvbt2 ? "dvbt2_" : "dvbt_";
        if (!array_key_exists($prefix.$id, Programmpaket::$all)) {
            global $DB;
            $paket = $DB->query("SELECT * FROM {$prefix}programmpaket WHERE id = ?", $id)[0];
            new Programmpaket($paket, $dvbt2);
        }
        return Programmpaket::$all[$prefix.$id];
    }
    
    public static function getPaketsByStateId($stateId, $dvbt2 = false) {
        $result = array();
        $prefix = $dvbt2 ? "dvbt2_" : "dvbt_";
        global $DB;
        $answer = $DB->query("SELECT {$prefix}transmitters.programmpaketId FROM {$prefix}transmitters LEFT JOIN {$prefix}programmpaket ON {$prefix}programmpaket.id = {$prefix}transmitters.programmpaketId WHERE {$prefix}transmitters.stateId = ? GROUP BY programmpaketId ORDER BY {$prefix}programmpaket.contraction", $stateId);
        foreach ($answer as $paket) {
            array_push($result, Programmpaket::getPaket($paket['programmpaketId'], $dvbt2));
        }
        return $result;
    }
    
    public static function getPaketsByLoc($id, $dvbt2 = false) {
        $prefix = $dvbt2 ? "dvbt2_" : "dvbt_";
        $result = array();
        global $DB;
        $answer = $DB->query("SELECT {$prefix}transmitters.programmpaketId FROM {$prefix}transmitters LEFT JOIN {$prefix}programmpaket ON {$prefix}programmpaket.id = {$prefix}transmitters.programmpaketId WHERE {$prefix}transmitters.location_id = ? GROUP BY {$prefix}transmitters.programmpaketId ORDER BY {$prefix}programmpaket.contraction", $id);
        foreach ($answer as $paket) {
            array_push($result, Programmpaket::getPaket($paket['programmpaketId'], $dvbt2));
        }
        return $result;
    }
}
