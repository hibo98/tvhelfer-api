<?php

class DABProgramm {
    
    public $id;
    public $name;
    public $langs; //array
    public $bandwidth;
    public $codec;
    public $region;
    public $anstalt;
    public $status;
    
    public function __construct($ProgrammDB) {
        $this->id = (int) $ProgrammDB['id'];
        $this->name = $ProgrammDB['name'];
        $this->langs = array($ProgrammDB['lang']);
        $this->bandwidth = $ProgrammDB['bandwidth'];
        $this->codec = $ProgrammDB['codec'];
        $this->region = $ProgrammDB['region'];
        $this->anstalt = $ProgrammDB['anstalt'];
        $this->status = $ProgrammDB['status'];
    }
}
