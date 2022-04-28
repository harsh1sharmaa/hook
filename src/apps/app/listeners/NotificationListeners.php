<?php

namespace App\Listeners;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use app\component;

class NotificationListeners extends Injectable
{
/**
 * this function cll hook to update added product
 *
 * @param Event $event
 * @param [type] $app
 * @return void
 */
    public function afterUpdate(Event $event, $app)
    {
        $doc = $event->getData();
        $obj = $this->hook;
        $obj->updateHook($doc);

       
    }
/**
 * this function cll hook to update edit product
 *
 * @param Event $event
 * @param [type] $app
 * @return void
 */
    public function editproduct(Event $event, $app)
    {

        $doc = $event->getData();
        $obj = $this->hook;
        $obj->editHook($doc);
    }
/**
 * this function cll hook to update quantity of product
 *
 * @param Event $event
 * @param [type] $app
 * @return void
 */
    public function updateQuantity(Event $event, $app)
    {
        $doc = $event->getData();
        $obj = $this->hook;
        $obj->editHook($doc);
        // $obj->updateQuantityHook($doc);
    }
}
