<?php

class Programm {
    
    public $id;
    public $name;
    public $langs; //array
    public $bandwidth;
    public $compression;
    public $crypt;
    public $region;
    public $px2;
    public $px2Time;
    public $px2Region;
    public $type;
    
    public function __construct($ProgrammDB) {
        $this->id = (int) $ProgrammDB['id'];
        $this->name = $ProgrammDB['name'];
        $this->langs = array($ProgrammDB['lang'], $ProgrammDB['lang2']);
        $this->bandwidth = $ProgrammDB['bandwidth'];
        $this->compression = $ProgrammDB['compression'];
        $this->crypt = $ProgrammDB['crypt'];
        $this->region = $ProgrammDB['region'];
        $this->px2 = $ProgrammDB['px2'];
        $this->px2Time = $ProgrammDB['timePx2'];
        $this->px2Region = $ProgrammDB['regionPx2'];
        $this->type = $ProgrammDB['type'];
    }
}
