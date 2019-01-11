<?php

require_once "connect.php";
require_once "class.location.php";
require_once "class.programmpaket_dab.php";

class DABTransmitter {

    public $id;
    public $location; //Class
    public $programmpaket; //Class
    public $channel;
    public $frequenz;
    public $polarisation;
    public $ERP_kW;
    public $ERP_dbW;
    public $status;
    
    private function __construct($TransmitterDB) {
        $this->id = (int) $TransmitterDB['id'];
        $this->location = Location::getLocation($TransmitterDB['locationId']);
        $this->programmpaket = DABProgrammpaket::getPaket($TransmitterDB['programmpaketId']);
        $this->channel = $TransmitterDB['channel'];
        $this->frequenz = $TransmitterDB['frequenz'];
        $this->polarisation = $TransmitterDB['polarisation'];
        $kW = (float) $TransmitterDB['ERP_kW'];
        $dbW = (float) $TransmitterDB['ERP_dbW'];
        if ($dbW == "") {
            $this->ERP_kW = $kW;
            $this->ERP_dbW = round(10 * log10($W), 2);
        } else {
            $this->ERP_kW = round(pow(10, ($dbW / 10)) / 1000, 2);
            $this->ERP_dbW = $dbW;
        }
        $this->status = $TransmitterDB['status'];
    }
    
    public static function getTransmitter($id) {
        global $DB;
        $query = $DB->query("SELECT * FROM dabTransmitters WHERE id = ?", $id);
        if (!empty($query)) {
            return new DABTransmitter($query[0]);
        } else {
            return null;
        }
    }
    
    public static function getTransmittersByLocation($id) {
        $result = array();
        global $DB;
        $answer = $DB->query("SELECT * FROM dabTransmitters WHERE locationId = ? GROUP BY channel ORDER BY id", $id);
        foreach($answer as $transmitter) {
            array_push($result, new DABTransmitter($transmitter));
        }
        return $result;
    }
    
    public static function getLocationsOfTransmittersByStateId($stateId) {
        $result = array();
        global $DB;
        $answer = $DB->query("SELECT locations.id FROM dabTransmitters LEFT JOIN locations ON locations.id = dabTransmitters.locationId WHERE dabTransmitters.stateId = '{$DB->escapeString($stateId)}' GROUP BY locations.id ORDER BY locations.name");
        foreach ($answer as $transmitter) {
            array_push($result, Location::getLocation($transmitter['id']));
        }
        return $result;
    }
    
    public static function getLocationsOfTransmittersByCountryId($countryId) {
        $result = array();
        global $DB;
        $answer = $DB->query("SELECT locations.id FROM dabTransmitters LEFT JOIN locations ON locations.id = dabTransmitters.locationId LEFT JOIN states ON states.id = dabTransmitters.stateId WHERE states.countryId = '{$DB->escapeString($countryId)}' GROUP BY locations.id ORDER BY locations.name");
        foreach ($answer as $transmitter) {
            array_push($result, Location::getLocation($transmitter['id']));
        }
        return $result;
    }
}
