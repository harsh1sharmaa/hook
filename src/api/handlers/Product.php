<?php

namespace Api\Handlers;

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Phalcon\Http\Request;
use GuzzleHttp\Client;
use Micro;


class Product extends Controller
{

    function allproducts()
    {
        // echo "All products";

        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->products;
        $ans = $collection->find(array(), array("limit" => 10));

        $results = array();

        foreach ($ans as $value) {

            array_push($results, $value);
        }

        return json_encode($results);
    }

    function searchByName($name)
    {
        if ($name == '') {

            return "enter valid name";
        } else {


            $m = $this->mongo;
            $db = $m->store;
            $collection = $db->products;
            $ans = $collection->find(array("name" => $name), array("limit" => 10));

            $results = array();

            foreach ($ans as $value) {

                array_push($results, $value);
            }
            if (count($results) > 0) {
                return json_encode($results);
            } else {
                return "there is no product";
            }
        }
    }

    function searchByNameAndLimit($name = "", $limit = 10)
    {
        if (((int)$limit) < 1) {

            return  "limit is not valid";
        }

        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->products;
        $ans = $collection->find(array("name" => $name), array("limit" => (int)$limit));

        $results = array();

        foreach ($ans as $value) {

            array_push($results, $value);
        }
        if (count($results) > 0) {
            return json_encode($results);
        } else {
            return "there is no product";
        }
    }

    function jumpToPage($pageno = 1, $limit = 10)
    {
        if (((int)$pageno) < 1) {

            return  "page not found";
        }
        if (((int)$limit) < 1) {

            return  "limit is not valid";
        }
        $skipdoc = (int)$limit * ((int)$pageno - 1);


        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->products;

        // echo "limit";
        $results = array();
        // $i = 0;
        $ans = $collection->find(array(), array('skip' => (int) $skipdoc, "limit" => (int)$limit));

        foreach ($ans as $value) {
            array_push($results, $value);
        }
        if (count($results) > 0) {
            return json_encode($results);
        } else {
            return "there is no product";
        }
    }




    //************************************************************************************************ */




    function search($name = "")
    {
        $delimiter = '%20';
        $words = explode($delimiter, $name);

        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->product;


        $respo = array();

        foreach ($words as $word) {

            $ans = $collection->find(array('$or' => [['info.name' => array('$regex' => $word)], ['info.category' => array('$regex' => $word)], ['variation' => ['$elemMatch' => ["color" => array('$regex' => $word)]]]]));

            $results = array();
            foreach ($ans as $value) {
                array_push($results, $value);
            }
            array_push($respo, $results);
        }

        return json_encode($respo);
        die;
    }








    public function postdata()
    {
        $data = $this->request->getpost();

        $doc = array("skuid" => $data['skuid'], "quantity" => $data['quantity'], "username" => $data['username'], "email" => $data['email'], "status" => "paid");
        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->order;
     
        $collection->insertOne($doc);

        $form = [
            'form_params' => array("skuid" => $data['skuid'], "quantity" => $data['quantity'])
        ];
        $client = new Client();
        $r = $client->request('POST', "192.168.2.24:8080/apps/hook/decrementofproduct", $form);
        // printf($r->getBody());
        // die();
    }

    function getorder()
    {

        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->order;

        $ans = $collection->find();

        $results = array();

        foreach ($ans as $value) {

            array_push($results, $value);
        }

        echo json_encode($results);
    }

    function update()
    {
        return $this->request->getPut('id');
        // return "hello";

        // return $id;

        // $m = $this->mongo;
        // $db = $m->store;
        // $collection = $db->order;

        // $collection->updateOne(array('_id' => new \MongoDB\BSON\ObjectId($id)), array('$set' => ['email' => "xyx@gmail"]));
        // return;
    }
}
