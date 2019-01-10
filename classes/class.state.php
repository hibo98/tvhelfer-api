<?php

require_once "connect.php";
require_once "class.country.php";

class State {
    
    private static $all = array();
    public $id;
    public $country; //class
    public $state;
    public $name;
    public $lastUpdateDVB_T;
    public $lastUpdateDVB_T2;
    public $lastUpdateDAB;
    
    function __construct($StateDB) {
        $this->id = (int) $StateDB['id'];
        $this->country = Country::getById($StateDB['countryId']);
        $this->state = $StateDB['state'];
        $this->name = $StateDB['name'];
        $this->lastUpdateDVB_T = $StateDB['lastUpdateDVBT'];
        $this->lastUpdateDVB_T2 = $StateDB['lastUpdateDVBT2'];
        $this->lastUpdateDAB = $StateDB['lastUpdateDAB'];
        State::$all[$this->id] = $this;
    }
    
    public function getTransmitters() {
        global $DB;
        return $DB->query("SELECT (SELECT count(*) FROM dabTransmitters WHERE dabTransmitters.state = states.state) as DAB, (SELECT count(*) FROM dvbt_transmitters WHERE dvbt_transmitters.stateId = states.id) as DVBT, (SELECT count(*) FROM dvbt2_transmitters WHERE dvbt2_transmitters.stateId = states.id) as DVBT2 FROM states WHERE countryId = ? AND id = ?", $this->country->id, $this->id);
    }
    
    public static function getById($id) {
        if (!array_key_exists($id, State::$all)) {
            global $DB;
            $query = $DB->query("SELECT * FROM states WHERE id = ?", $id);
            if (!empty($query)) {
                new State($query[0]);
            } else {
                return null;
            }
        }
        return State::$all[$id];
    }
    
    public static function getByCountry($countryId) {
        global $DB;
        $result = array();
        $states = $DB->query("SELECT * FROM states WHERE countryId = ? ORDER BY id", $countryId);
        foreach ($states as $state) {
            array_push($result, new State($state));
        }
        return $result;
    }
}
