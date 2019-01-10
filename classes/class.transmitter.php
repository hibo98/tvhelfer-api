<?php

require_once "connect.php";
require_once "class.location.php";
require_once "class.programmpaket.php";

class Transmitter {

    public $id;
    public $location; //Class
    public $programmpaket; //Class
    public $channel;
    public $frequenz; //Calculated
    public $polarisation;
    public $ERP_kW;
    public $ERP_dbW;
    public $status;
    public $onlineSince;
    
    private function __construct($id, $locationId, $programmpaketId, $channel, $polarisation, $ERP_kW, $ERP_dbW, $status, $onlineSince, $dvbt2) {
        $this->id = (int) $id;
        $this->location = Location::getLocation($locationId);
        $this->programmpaket = Programmpaket::getPaket($programmpaketId, $dvbt2);
        $this->channel = $channel;
        $this->frequenz = ($this->channel >= 21 ? $this->channel * 8 + 306 : $this->channel * 7 + 142.5);
        $this->polarisation = $polarisation;
        $W = (float) ($ERP_kW * 1000);
        if ($ERP_dbW == "") {
            $this->ERP_kW = ((float) $ERP_kW) . " kW";
            $this->ERP_dbW = round(10 * log10($W), 2) . " dbW";
        } else {
            $this->ERP_kW = round(pow(10, ($ERP_dbW / 10)) / 1000, 2) . " kW";
            $this->ERP_dbW = ((float) $ERP_dbW) . " dbW";
        }
        $this->status = $status;
        $this->onlineSince = empty($onlineSince) ? "-" : date("d.m.Y", $onlineSince);
    }
    
    public static function getTransmitter($id, $dvbt2 = false) {
        $result = array();
        $table = $dvbt2 ? "dvbt2_transmitters" : "dvbt_transmitters";
        global $DB;
        $answer = $DB->query("SELECT * FROM $table WHERE id = ?", $id);
        foreach($answer as $transmitter) {
            array_push($result, new Transmitter($transmitter['id'], $transmitter['location_id'], $transmitter['programmpaketId'], $transmitter['channel'], $transmitter['polarisation'], $transmitter['ERP'], $transmitter['ERP_dbW'], $transmitter['status'], $transmitter['onlineSince'], $dvbt2));
        }
        return $result;
    }
    
    public static function getTransmittersByLocation($id, $dvbt2 = false) {
        $result = array();
        $table = $dvbt2 ? "dvbt2_transmitters" : "dvbt_transmitters";
        global $DB;
        $answer = $DB->query("SELECT * FROM $table WHERE location_id = ? ORDER BY id", $id);
        foreach($answer as $transmitter) {
            array_push($result, new Transmitter($transmitter['id'], $transmitter['location_id'], $transmitter['programmpaketId'], $transmitter['channel'], $transmitter['polarisation'], $transmitter['ERP'], $transmitter['ERP_dbW'], $transmitter['status'], $transmitter['onlineSince'], $dvbt2));
        }
        return $result;
    }
    
    public static function getLocationsOfTransmittersByStateId($stateId, $dvbt2 = false) {
        $result = array();
        $table = $dvbt2 ? "dvbt2_transmitters" : "dvbt_transmitters";
        global $DB;
        $answer = $DB->query("SELECT locations.id FROM $table LEFT JOIN locations ON locations.id = $table.location_id WHERE $table.stateId = '{$DB->escapeString($stateId)}' GROUP BY locations.id ORDER BY locations.name");
        foreach ($answer as $transmitter) {
            array_push($result, Location::getLocation($transmitter['id']));
        }
        return $result;
    }
    
    public static function getLocationsOfTransmittersByCountryId($countryId, $dvbt2 = false) {
        $result = array();
        $table = $dvbt2 ? "dvbt2_transmitters" : "dvbt_transmitters";
        global $DB;
        $answer = $DB->query("SELECT locations.id FROM $table LEFT JOIN locations ON locations.id = $table.location_id LEFT JOIN states ON states.id = $table.stateId WHERE states.countryId = '{$DB->escapeString($countryId)}' GROUP BY locations.id ORDER BY locations.name");
        foreach ($answer as $transmitter) {
            array_push($result, Location::getLocation($transmitter['id']));
        }
        return $result;
    }
}
