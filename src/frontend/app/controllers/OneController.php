<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use app\component\Myescaper;

use GuzzleHttp\Client;



class OneController extends Controller
{



    public function addorderAction()
    {

        $data = $this->request->getPost();

        $products = $this->getproduct();


        if (isset($data['submit'])) {

            $id = $data['skuid'];
            $quantity = $data['quantity'];
            $email = $data['email'];
            $uname = $data['uname'];
            $token = $data['token'];

            $arr = array("skuid" => $id, "quantity" => $quantity, "username" => $uname, "email" => $email, "token" => $token);
            // $header = array("Content-Type" => "application/json");

            $form = [
                'form_params' => $arr
            ];

            $client = new Client();
            $r = $client->request('POST', "192.168.2.24:8080/api/invoices/post?key=$token", $form);
        }
        $this->view->message = $products;
    }




    public function getproduct()
    {

        $m = $this->mongo;
        $db = $m->front;
        $collection = $db->product;

        $ans = $collection->find([], array("limit" => 50));
        $ans = $ans->toArray();

        return json_decode(json_encode($ans), 1);
    }

    public function reciveAction()
    {

        $data = $this->request->getpost();
        $hdr = $this->request->getHeaders();

        // return $data;
        // echo "<pre>";
        // print_r($data);
        // die();
        //************************************************************************************************ */
        $m = $this->mongo;
        $db = $m->front;
        $collection = $db->product;

        // $doc=array("productname"=>$data['productname'],);
        $doc = array(
            "productname" => $data['productname'],
            "sku" => $data['sku'],
            "price" => $data['price'],
            "quantity" => $data['quantity'],
        );
        // return $doc;


        // if ($hdr['key'] == '123') {
        $ans = $collection->insertOne($doc);
        // }
    }
    public function editreciveAction()
    {

        $data = $this->request->getput();
        // $hdr = $this->request->getHeaders();

        $m = $this->mongo;
        $db = $m->front;
        $collection = $db->product;

        $sku = $data['sku'];
        $quantity = $data['quantity'];
        // if (isset($data['price'])) {
        //     $price = $data['price'];

        //     $ans = $collection->updateOne(array('sku' => $sku), array('$set' => ['quantity' => $quantity, 'price' => $price]));
        // } else {
        //     $ans = $collection->updateOne(array('sku' => $sku), array('$set' => ['quantity' => $quantity]));
        // }
        if (isset($data['price'])) {
            $price = $data['price'];

            $ans = $collection->updateOne(array('sku' => $sku), array('$set' => ['price' => $price]));
        }
        if (isset($data['quantity'])) {
            $ans = $collection->updateOne(array('sku' => $sku), array('$set' => ['quantity' => $quantity]));
        }
    }

    public function showproductAction()
    {

        $m = $this->mongo;
        $db = $m->front;
        $collection = $db->product;

        $ans = $collection->find();

        $ans = $ans->toArray();
        json_decode(json_encode($ans), true);
        // echo "<pre>";
        //         print_r($ans);
        //         die();

        $this->view->message = $ans;
    }

    public function testAction()
    {

        $data = $this->request->getpost("data");
        $m = $this->mongo;
        $db = $m->front;
        $collection = $db->product;
        $collection->insertOne($data);
    }
    public function test2Action()
    {

        $id = $this->request->getpost("product_sku");
        $data = $this->request->getpost("data");



        $m = $this->mongo;
        $db = $m->front;
        $collection = $db->product;
        $collection->updateOne(array("product_sku" => $id), array('$set' => $data));
    }
    public function test3Action()
    {

        $data = $this->request->getpost();
        // $data = $this->request->getpost("data");

        $data = array_merge($data, ['_id' => (new ObjectId($data[0]['_id']['$oid']))]);



        $m = $this->mongo;
        $db = $m->front;
        $collection = $db->product;
        $collection->insertOne($data);
        // $collection->updateOne(array("_id" => new ObjectId($id)), array('$set' => $data));
    }
}
