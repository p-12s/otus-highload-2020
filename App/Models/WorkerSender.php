<?php

namespace App\Models;

use MySQLi;
use App\Config;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class WorkerSender extends \Core\Model
{
    private $connection;
    public $channel;

    public function __construct() {
        $this->connection = new AMQPStreamConnection(
            Config::RABBITMQ_HOST, Config::RABBITMQ_PORT,
            Config::RABBITMQ_USER, Config::RABBITMQ_PASSWORD);
        $this->channel = $this->connection->channel();
    }

    public function __destruct() {
        $this->channel->close();
        $this->connection->close();
        unset($this->channel, $this->connection);
    }

    // example: https://ruseller.com/lessons.php?rub=37&id=2172
    public function send($queueName, $message) { // м/б/ передавать ид новости? или лучше самодостаточные данные
        $this->channel->queue_declare(
            $queueName, // Имя очереди может содержать до 255 байт UTF-8 символов
            false, // passive - может использоваться для проверки того, инициирован ли обмен, без того, чтобы изменять состояние сервера
            true, // durable - убедимся, что RabbitMQ никогда не потеряет очередь при падении - очередь переживёт перезагрузку брокера
            false, // exclusive - используется только одним соединением, и очередь будет удалена при закрытии соединения
            false // autodelete - очередь удаляется, когда отписывается последний подписчик
        );
        $msg = new AMQPMessage($message);
        $this->channel->basic_publish(
            $msg, // сообщение
            '', // обмен
            $queueName // ключ маршрутизации (очередь)
        );
    }

    public function worker($queueName) {
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
            sleep(substr_count($msg->body, '.'));
            echo " [x] Done\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    private function workerCallback($msg)
    {
        echo ' [x] Received ', $msg->body, "\n";
        sleep(substr_count($msg->body, '.'));
        echo " [x] Done\n";
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }
}
