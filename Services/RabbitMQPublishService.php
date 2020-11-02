<?php

namespace OldSound\RabbitMqBundle\Services;

use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RabbitMQPublishService {

    /** @var  Connection $connection */
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function basicPublish($producer, $content, $routingKey = null) {
        $this->container->get('old_sound_rabbit_mq.'.$producer.'_producer')->publish(json_encode($content), $routingKey);
    }

    public function publish($producer, $section, $scope, $action, $content, $extra = []) {

        if(!$producer || !$section || !$scope || !$action)
            throw new \Exception("The routing key needs the 'producer', 'section', 'scope' and 'action' parameters to work with this method.");

        $routingKey = $producer . '.' . $section . '.' . $scope . '.' . $action;

        $message = array(
            'content' => $content,
            'date' => date('Y-m-d H:i:s')
        );

        foreach ($extra as $key => $value) {
            $message[$key] = $value;
        }

        $this->container->get('old_sound_rabbit_mq.'.$producer.'_producer')->publish(json_encode($message), $routingKey);
    }

    public function publishWithRoutingKey($producer, $content, $routingKey, $extra = []) {

        $message = array(
            'content' => $content,
            'date' => date('Y-m-d H:i:s')
        );

        foreach ($extra as $key => $value) {
            $message[$key] = $value;
        }

        $this->container->get('old_sound_rabbit_mq.'.$producer.'_producer')->publish(json_encode($message), $routingKey);
    }

}