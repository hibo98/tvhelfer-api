<?php

class MySQL {

    public $db;
    public $state;

    public function __construct($host, $username, $passwd, $db, $port = "3306") {
        try {
            $this->db = new PDO("mysql:dbname={$db};host={$host}:{$port}", $username, $passwd, array(PDO::ATTR_PERSISTENT => true));
        } catch (PDOException $e) {
            $this->error(array("code" => "MY_CONNECT_" . $e->getCode(), "msg" => "Could not connect to MySQL Server:<br />" . $e->getMessage()));
            $this->state = false;
            return;
        }
        $this->query("SET NAMES utf8");
        $this->state = true;
    }

    private function error($error) {
        var_dump($error);
    }

    public function __destruct() {
        if (!$this->state) {
            return;
        }
        $this->db = null;
    }

    public function query($query, ...$param) {
        $prep = $this->db->prepare($query);
        $execute = $prep->execute($param);
        return $this->makeResult($execute, $prep);
    }

    public function getInsertId() {
        return $this->db->lastInsertId();
    }

    public function printLastSQLError() {
        $this->error(array("code" => "MY_QUERY_" . $this->db->errorCode(), "msg" => $this->db->errorInfo()));
    }

    public function lastSQLError() {
        return $this->db->errorInfo();
    }

    private function makeResult(bool $execute, PDOStatement $prep) {
        if ($execute === false) {
            return false;
        } else if ($execute === true) {
            $array = array();
            while ($line = $prep->fetch(PDO::FETCH_ASSOC)) {
                array_push($array, $line);
            }
            return $array;
        }
    }

}
