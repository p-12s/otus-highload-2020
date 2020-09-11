<?php

namespace App\Models;

use MySQLi;
use App\Config;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class WorkerReceiver extends \Core\Model
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

    /** Обрабатывает входящие запросы на генерацию накладных в PDF  и их отправку через email
     */
    public function listen() { // TODO ОСТАНОВИЛСЯ ТУТ- ПЕРЕДЕЛАТЬ РЕСИВЕР ПОД СЕБЯ (ПУСТЬ КЛАДЕТ В КЕШ ПОШПИСЯТАМ МЕССАДЖИ)
        $this->channel->queue_declare(
            'invoice_queue',	#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
            false,      	#passive - может использоваться для проверки того, инициирован ли обмен, без того, чтобы изменять состояние сервера
            true,      	#durable - убедимся, что RabbitMQ никогда не потеряет очередь при падении - очередь переживёт перезагрузку брокера
            false,      	#exclusive - используется только одним соединением, и очередь будет удалена при закрытии соединения
            false       	#autodelete - очередь удаляется, когда отписывается последний подписчик
        );

        /**
         * не отправляем новое сообщение на обработчик, пока он
         * не обработал и не подтвердил предыдущее. Вместо этого
         * направляем сообщение на любой свободный обработчик
         */
        $this->channel->basic_qos(
            null,   #размер предварительной выборки - размер окна предварительнйо выборки в октетах, null означает “без определённого ограничения”
            1,  	#количество предварительных выборок - окна предварительных выборок в рамках целого сообщения
            null	#глобальный - global=null означает, что настройки QoS должны применяться для получателей, global=true означает, что настройки QoS должны применяться к каналу
        );

        /**
         * оповещает о своей заинтересованности в получении
         * сообщений из определённой очереди. В таком случае мы
         * говорим, что они регистрируют получателя, или устанавливают
         * подписку на очередь. Каждый получатель (подписка) имеет
         * идентификатор, называемый “тег получателя”.
         */
        $this->channel->basic_consume(
            'invoice_queue',    	#очередь
            '',                  #тег получателя - Идентификатор получателя, валидный в пределах текущего канала. Просто строка
            false,               #не локальный - TRUE: сервер не будет отправлять сообщения соединениям, которые сам опубликовал
            false,               #без подтверждения - false: подтверждения включены, true - подтверждения отключены. отправлять соответствующее подтверждение обработчику, как только задача будет выполнена
            false,                 #эксклюзивная - к очереди можно получить доступ только в рамках текущего соединения
            false,                 #не ждать - TRUE: сервер не будет отвечать методу. Клиент не должен ждать ответа
            array($this, 'process')	#функция обратного вызова - метод, который будет принимать сообщение
        );

        while(count($this->channel->callbacks)) {
            $this->log->addInfo('Слежу за входящими сообщениями');
            $this->channel->wait();
        }
    }

    /** Обработка полученного запроса
     * @param AMQPMessage $msg
     */
    public function process(AMQPMessage $msg) {
        $this->generatePdf()->sendEmail();

        /**
         * Если получатель умирает, не отправив подтверждения, брокер
         * AMQP пошлёт сообщение другому получателю. Если свободных
         * на данный момент нет - брокер подождёт до тех пор, пока
         * освободится хотя-бы один зарегистрированный получатель
         * на эту очередь, прежде чем попытаться заново доставить
         * сообщение
         */
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }

    /**
     * Генерирует PDF файл с накладной
     *
     * @return WorkerReceiver
     */
    private function generatePdf()
    {
        /**
         * Симулируем время обработки PDF. Это занимает от 2 до 5 секунд
         */
        sleep(mt_rand(2, 5));
        return $this;
    }

    /**
     * Отправляет письмо
     *
     * @return WorkerReceiver
     */
    private function sendEmail()
    {
        /**
         * Симулируем время отправки письма. Занимает 1-3 секунды
         */
        sleep(mt_rand(1,3));
        return $this;
    }
}