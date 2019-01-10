<?php

require_once "connect.php";
require_once "class.state.php";

class Location {

    private static $all = array();
    public $id;
    public $name;
    public $state; //class
    public $latitude;
    public $longitude;
    public $status;
    public $operator;
    public $aboveSeaLevel;

    private function __construct($locationDB) {
        $this->id = (int) $locationDB['id'];
        $this->name = $locationDB['name'];
        $this->state = State::getById($locationDB['stateId']);
        $this->latitude = $locationDB['latitude'] == null ? null : (double) $locationDB['latitude'];
        $this->longitude = $locationDB['longtitude'] == null ? null : (double) $locationDB['longtitude'];
        $this->status = $locationDB['status'];
        $this->operator = $locationDB['operator'];
        $this->aboveSeaLevel = $locationDB['aboveSeaLevel'] == null ? null : (int) $locationDB['aboveSeaLevel'];
        Location::$all[$this->id] = $this;
    }

    public static function getLocationsByStateId($stateId) {
        global $DB;
        $result = array();
        $answer = $DB->query("SELECT * FROM locations WHERE stateId = ? ORDER BY id", $stateId);
        foreach ($answer as $loc) {
            array_push($result, new Location($loc));
        }
        return $result;
    }

    public static function getLocationsByCountryId($countryId) {
        global $DB;
        $result = array();
        $answer = $DB->query("SELECT locations.* FROM locations LEFT JOIN states ON states.id = locations.stateId WHERE states.countryId = ? ORDER BY name", $countryId);
        foreach ($answer as $loc) {
            array_push($result, new Location($loc));
        }
        return $result;
    }

    public static function getLocation($id) {
        if (!array_key_exists($id, Location::$all)) {
            global $DB;
            $query = $DB->query("SELECT * FROM locations WHERE id = ?", $id);
            if (!empty($query)) {
                new Location($query[0]);
            } else {
                return null;
            }
        }
        return Location::$all[$id];
    }
    
    public static function getAll() {
        global $DB;
        $query = $DB->query("SELECT * FROM locations ORDER BY id");
        foreach ($query as $entry) {
            new Location($entry);
        }
        return Location::$all;
    }

}
