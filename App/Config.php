<?php

namespace App;

class Config
{
    // запросы на запись/запись-с-последующим-чтением отправляем на мастер
    private $masterIp = 'your_ip_master';
    // запросы на чтение отправляем на слейвы
    private $slaveIps = array('your_ip_1', 'your_ip_2');

    const DB_HOST = 'localhost';
    const DB_NAME = 'social';
    const DB_USER = 'social';
    const DB_PASSWORD = 'your_password';
    const SHOW_ERRORS = true;

    // конфиги для шардов
    public $shard_1 = array('db_host' => 'your_ip_1', 'db_name' => 'social', 'db_user' => 'social', 'db_password' => 'your_password');
    public $shard_2 = array('db_host' => '52.59.191.28', 'db_name' => 'social', 'db_user' => 'social', 'db_password' => 'your_password');

    // TODO как это упаковать в метод? дай рид конфиги, дай врайт конфиги
    // 2 шарды на запись
    // 2 слейва на чтение

    /*
     * <?
$master = mysql_connect('10.10.0.1', 'root', 'pwd');
$slaves = [
	'10.10.0.2',
	'10.10.0.3',
	'10.10.0.4',
];
$slave = mysql_connect($slaves[array_rand($slaves)], 'root', 'pwd');

# ...
mysql_query('INSERT INTO users ...', $master);

    */
    //public get
}
