<?php


namespace app\component;

use Phalcon\Di\Injectable;
use GuzzleHttp\Client;


// http://localhost:8080/frontend/one/addorder
class Hook extends Injectable
{

    public function updateHook($doc)
    {
        // print_r($doc);
        // die();

        $form = [
            'form_params' => $doc
        ];
        // $this->sendToall($form);
        $this->sendToallUrl($form, "add");
    }
    public function editHook($doc)
    {
        // print_r($doc);
        // die();

        $form = [
            'form_params' => $doc
        ];
        // $this->editToall($form);
        $this->sendToallUrl($form, "update");
    }
    // public function updateQuantityHook($doc)
    // {
    //     // print_r($doc);
    //     // die();

    //     $form = [
    //         'form_params' => $doc
    //     ];
    //     // $this->editToall($form);
    //     $this->sendToallUrl($form, "update");
    // }

    // public function  editToall($form)
    // {
    //     $client = new Client();
    //     $m = $this->mongo;
    //     $db = $m->store;
    //     $collection = $db->edithook;
    //     $urls = $collection->find();
    //     foreach ($urls as $key => $value) {

    //         $r = $client->request('POST', "$value->url", $form);
    //     }
    // }

    // public function sendToall($form)
    // {
    //     $client = new Client();
    //     $m = $this->mongo;
    //     $db = $m->store;
    //     $collection = $db->hookuser;
    //     $urls = $collection->find();

    //     foreach ($urls as $key => $value) {
    //         $r = $client->request('POST', "$value->url", $form);
    //     }
    // }

    public function sendToallUrl($form, $action)
    {
        $client = new Client();
        $m = $this->mongo;
        $db = $m->store;
        $logger = $this->logger;


        switch ($action) {




            case 'add':
                $collection = $db->hookuser;
                $urls = $collection->find();


                foreach ($urls as $key => $value) {
                    $logger->info("add to " . $value->username);


                    $r = $client->request('POST', "$value->url", $form);
                }
                break;
            case 'update':
                $collection = $db->edithook;
                $urls = $collection->find();
                foreach ($urls as $key => $value) {
                    $logger->info("updated to " . $value->username);


                    $r = $client->request('POST', "$value->url", $form);
                }
                break;
        }
    }
}
