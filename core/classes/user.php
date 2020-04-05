<?php

class User  {
    protected $pdo;

    function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function checkInput($variable) {
        $variable = htmlspecialchars($variable);
        $variable = trim($variable);
        $variable = stripslashes($variable);
        return $variable;
    }

    public function checkEmail($variable) {
        $statement = $this->pdo->prepare("SELECT email FROM user WHERE email = :email");
        $statement->bindParam(":email", $variable, PDO::PARAM_STR);
        $statement->execute();
        return $statement->rowCount() == 1;
    }

    public function create($table, $fields = array()) {
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
