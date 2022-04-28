<?php

namespace App\Listeners;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use app\component;

class NotificationListeners extends Injectable
{

    public function afterUpdate(Event $event, $app)
    {
        $doc = $event->getData();



        // print_r($doc);
        // die();

        $obj = $this->hook;
        $obj->updateHook($doc);

        // die('logger');
    }
}
