<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use app\component\Myescaper;
use app\component\Hookuser;
use app\component\Helper;

use GuzzleHttp\Client;



class HookController extends Controller
{
    /**
     * this function add the  products and fire event 
     *
     * @return void
     */
    public function addproductAction()
    {
        // $data = $this->config;

        // echo $data->data->name;
        // die;

        $data = $this->request->getpost();

        if (isset($data['submit'])) {

            $productname = $data['productname'];
            $sku = $this->generateRandomStringAction();
            // $sku = $data['sku'];
            $price = $data['price'];
            $quantity = $data['quantity'];
            $doc = array(
                "productname" => $data['productname'],
                "sku" => $sku,
                "price" => $data['price'],
                "quantity" => $data['quantity'],
            );
            // print_r($doc);
            // die;

            $m = $this->mongo;
            $db = $m->store;
            $collection = $db->products;

            $collection->insertOne($doc);


            $eventsManager = $this->di->get('EventsManager');
            $eventsManager->fire('notification:afterUpdate', $this, $doc);
        }
    }
    /**
     * this function registers the hooks url
     *
     * @return void
     */
    public function getHookAction()
    {

        $data = $this->request->getPost();

        if (isset($data['submit'])) {

            $hlpr = new Helper();
            $role = $hlpr->decodeRole($data['token']);
            if ($role != 'admin') {

                $message = "invalid token";
            } else {
                $obj = new Hookuser();

                $obj->setindb($data);
            }
        }
        if (isset($data['edit'])) {
            // echo "hello";
            // die;
            $hlpr = new Helper();
            $role = $hlpr->decodeRole($data['token']);
            if ($role != 'admin') {

                $message = "invalid token";
            } else {
                $obj = new Hookuser();

                $obj->setEditUrlindb($data);
            }
        }
        $this->view->message = $message;
    }
    /**
     * this function recive the count of order and decrement the quantity
     *
     * @return void
     */
    public function decrementofproductAction()
    {



        $data = $this->request->getPost();

        $sku = $data['skuid'];
        $quantity = $data['quantity'];
        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->products;
        $product = $collection->findOne(array('sku' => $sku));
        $oldquantity = (int) $product['quantity'];
        $newQuantity = $oldquantity - (int)$quantity;
        if ($newQuantity <= 0) {
            $newQuantity = 0;
        }
        $collection->updateOne(array('sku' => $sku), array('$set' => ['quantity' => $newQuantity]));
        $eventsManager = $this->di->get('EventsManager');
        $doc = array('sku' => $sku, 'quantity' => $newQuantity);
        $eventsManager->fire('notification:updateQuantity', $this, $doc);
    }
    /**
     * this function display all product
     *
     * @return void
     */
    public function displayproductsAction()
    {

        // echo "rerere";
        // die;


        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->products;
        $ans = $collection->find();
        $ans = $ans->toArray();
        json_decode(json_encode($ans), 1);

        $this->view->message = $ans;
    }

    public function updateproductAction()
    {

        // echo phpinfo();
        // die;

        // $this->logger->info('update product');
        $obj = $this->logger;

        $obj->info("product updated");


        $data = $this->request->getPost();

        if (isset($data['update'])) {


            $skuid = $data['skuid'];
            $quantity = $data['quantity'];
            $price = $data['price'];
            $m = $this->mongo;

            $db = $m->store;
            $collection = $db->products;

            $collection->updateOne(array('sku' => $skuid), array('$set' => ['quantity' => $quantity, 'price' => $price]));

            $doc = array(
                "sku" => $skuid,
                "quantity" => $quantity,
                "price" => $price
            );

            $eventsManager = $this->di->get('EventsManager');
            $eventsManager->fire('notification:editproduct', $this, $doc);
        }

        $this->view->message = $data;
    }

    public function generateRandomStringAction()
    {
        $data = $this->config;

        // echo $data->data->name;
        $length = 15;
        $characters = $data->data->str;
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
