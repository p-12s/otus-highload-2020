<?php

$hostDetails = 'mysql:host=localhost; dbname=clonebook; charset=utf8';
$userAdmin = 'clonebook';
$pass = 'clonebook_password';

try{
    $pdo = new PDO($hostDetails, $userAdmin, $pass);
} catch(PDOException $e){
    echo 'Connection error!' . $e->getMessage();
}