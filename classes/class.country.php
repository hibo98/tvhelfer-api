<?php

require_once "connect.php";

class Country {
    
    private static $all = array();
    public $id;
    public $country;
    public $name;
    
    function __construct($CountryDB) {
        $this->id = (int) $CountryDB['id'];
        $this->country = $CountryDB['country'];
        $this->name = $CountryDB['name'];
        Country::$all[$this->id] = $this;
    }
    
    public static function getById($id) {
        if (!array_key_exists($id, Country::$all)) {
            global $DB;
            $query = $DB->query("SELECT * FROM countries WHERE id = ?", $id);
            if (!empty($query)) {
                new Country($query[0]);
            } else {
                return null;
            }
        }
        return Country::$all[$id];
    }
    
    public static function getAll() {
        global $DB;
        $query = $DB->query("SELECT * FROM countries ORDER BY id");
        foreach ($query as $entry) {
            new Country($entry);
        }
        return Country::$all;
    }
}
