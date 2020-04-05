<?php

class DbRecord  {
    protected $pdo;

    function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($table, $fields = array()) { // TODO стремный непонятный код
        $columns = implode(',', array_keys($fields));
        $values = ':'.implode(', :', array_keys($fields));
        $sql = "INSERT INTO {$table}({$columns}) VALUES ({$values})";

        if($stmt = $this->pdo->prepare($sql)) {
            foreach($fields as $key => $data) {
                $stmt->bindValue(':'. $key, $data);
            }
            $stmt->execute();
            return $this->pdo->lastInsertId();
        }
    }
}
