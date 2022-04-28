<?php

namespace app\component;

use Phalcon\Mvc\Controller;

class Hookuser extends Controller
{

    function setindb($data)
    {
        // echo ' Exception';
        // print_r($data);
        // die();
        $doc = array("username" => $data['username'], "url" => $data['url'], "key" => $data['key']);

        $m = $this->mongo;

        $db = $m->store;

        $collection = $db->hookuser;
        $collection->insertOne($doc);
    }
    function setEditUrlindb($data)
    {
        // echo ' Exception';
        // print_r($data);
        // die();
        $doc = array("username" => $data['username'], "url" => $data['url'], "key" => $data['key']);

        $m = $this->mongo;

        $db = $m->store;

        $collection = $db->edithook;
        $collection->insertOne($doc);
    }
}
