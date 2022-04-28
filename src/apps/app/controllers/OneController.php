<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use app\component\Myescaper;

use GuzzleHttp\Client;



// class OneController extends Controller
{

    public function loginAction()
    {
        $eventsManager = $this->di->get('EventsManager');
        // $obj=$this->notification;
        // $obj->fire('afterSend');



        $eventsManager->fire('notification:afterUpdate', $eventsManager);

        $data = $this->request->getPost();
        if (isset($data['submit'])) {
            $escp = new Myescaper();
            $email = $escp->sanitize($data['email']);
            $password = $escp->sanitize($data['password']);

            if ($email == "harsh" && $password == "123") {

                $this->response->redirect('admin/one/insert');
            }
        }

        $this->view->message = "hello";
    }

    public function getDataAction()
    {
        $m = $this->mongo;

        $db = $m->store;

        $collection = $db->products;
        $ans = $collection->find();

        // echo "<pre>";
        // print_r($ans);
        // die();
        return $ans;
    }

    public function productsAction()
    {

        $ans = $this->getDataAction();

        $this->view->message = $ans;
    }

    public function insertAction()
    {
        echo "helo insert";
        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->products;

        $data = $this->request->getPost();
        if (isset($data['search'])) {
            $productname = $data['productname'];
            $success = $collection->find(array("info.name" => $productname));
            $this->view->message = $success;
        } elseif (isset($data['submit'])) {

            $doc = $this->createdocAction($data);

            $success = $collection->insertOne($doc);
        } else {

            $data = $this->getDataAction();

            $this->view->message = $data;
        }
    }

    /**
     * this function delete the documents from collection
     *
     * @return void
     */
    public function deleteAction()
    {
        $data = $this->request->get();
        if (isset($data['submit'])) {
            $id = $data['id'];
            $this->deleteHelperAction($id);
        }
        $this->response->redirect("apps/one/insert");
    }


    /**
     * this function return the document of given id
     *
     * @return void
     */
    public function getdatabyidAction()
    {

        $id = $this->request->getpost('id');
        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->products;
        $success = $collection->findOne(array("_id" => new \MongoDB\BSON\ObjectId("$id")));
        return  json_encode($success);
        // die;
    }
    /**
     * this function insert documents into the collection
     *
     * @return void
     */
    public function orderAction()
    {

        $data = $this->getDataAction();
        $post = $this->request->getPost();
        if (isset($post['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $doc = $this->createOrderDocAction($post);
            $form = [
                'form_params' => $doc
            ];

            $client = new Client();
            $r = $client->request('POST', "192.168.2.24:8080/api/invoices/post?key=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vZXhhbXBsZS5vcmciLCJhdWQiOiJodHRwOi8vZXhhbXBsZS5jb20iLCJpYXQiOjEzNTY5OTk1MjQsIm5iZiI6MTM1NzAwMDAwMCwicm9sZSI6ImFkbWluIn0.zx9551R5B_KMBtr2KyihvlknTVoPMm2D_Bxss7d-GEs", $form);

            // $m = $this->mongo;
            // $db = $m->store;
            // $collection = $db->order;
            // $success = $collection->insertOne($doc);
            $this->response->redirect("apps/one/order");
        }

        $this->view->message = $data;
    }

    public function orderlistAction()
    {
        $data = $this->request->getPost();
        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->order;
        if (isset($data['status'])) {

            $status = $data['status'];
            $orderId = $data['orderId'];

            $collection->updateOne(["_id" => new \MongoDB\BSON\ObjectId("$orderId")], ['$set' => ["status" => $status]]);
            $ans = $collection->find();
            $this->view->message = $ans;
        } elseif (isset($data['submit'])) {

            $data = $this->request->getPost();
            $status = $data['getstatus'];
            $date = $data['filterdate'];

            $filterdata = $this->filterAction($status, $date);
            $this->view->message = $filterdata;
        } else {

            $ans = $collection->find();
            $this->view->message = $ans;
        }
    }

    /**
     * this function filter based on condition
     *
     * @param [type] $status
     * @param [type] $date
     * @return void
     */
    public function filterAction($status, $date)
    {

        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->order;
        $todaydate = date("Y-m-d");
        $selecteddate = $todaydate;
        if ($date == "today") {
            $selecteddate = $todaydate;
        } elseif ($date == "this week") {
            $selecteddate = date('d-m-Y', strtotime($todaydate . ' -7 days'));
        } elseif ($date == "this month") {
            $selecteddate = date('d-m-Y', strtotime($todaydate . ' -30 days'));
        } else {

            $data = $this->request->getpost();
            echo "<pre>";
            print_r($data);
            $stdate = $data['stdate'];
            $endate = $data['endate'];
            $ans = $collection->find(['$and' => [["created" => ['$lte' => $endate]], ["created" => ['$gte' => $stdate]]]]);
        }
        $ans = $collection->find(['$and' => [["status" => "$status"], ['$and' => [["created" => ['$lte' => $todaydate]], ["created" => ['$gte' => $selecteddate]]]]]]);
        return $ans;
    }
    /**
     * this function create the document of inserted product
     *
     * @param [type] $data
     * @return void
     */
    public function createdocAction($data)
    {


        $myescap = new Myescaper();

        $addiCount = $data['max'];
        $variCount = $data['varimax'];
        $name = $myescap->sanitize($data['name']);
        $myescap->sanitize($name);

        $category = $myescap->sanitize($data['category']);
        $price = $myescap->sanitize($data['price']);
        $stock = $myescap->sanitize($data['stock']);
        $detail = array("name" => $name, "category" => $category, "price" => $price, "stock" => $stock);
        $additional = array();
        $variation = array();

        for ($i = 0; $i < $addiCount; $i++) {

            $additional = $additional + [$myescap->sanitize($data["atname" . $i]) =>  $myescap->sanitize($data["atvalue" . $i])];
        }

        for ($i = 0; $i < $variCount; $i++) {

            $attributecount = $data['attricount' . $i];
            $objOfVariation = array();
            for ($j = 0; $j <= $attributecount; $j++) {
                $key = $myescap->sanitize($data['attriname' . $i . '' . $j]);
                $val = $myescap->sanitize($data['attrival' . $i . '' . $j]);


                $objOfVariation = $objOfVariation + [$key => $val];
            }
            array_push($variation, $objOfVariation);
        }
        $doc = array("info" => $detail, "additional" => $additional, "variation" => $variation);

        return $doc;
    }
    /**
     * this function create document of order 
     *
     * @param [type] $post
     * @return void
     */
    public function createOrderDocAction($post)
    {
        $myescap = new Myescaper();


        $productId = $post['id'];
        // $variation = $post['variname'];
        $coustomername = $myescap->sanitize($post['coustomername']);
        $quantity = $myescap->sanitize($post['quantity']);
        $createdate = $myescap->sanitize(date("Y-m-d"));
        $doc = array("productId" => $productId, "coustomername" => $coustomername, "quantity" => $quantity, "created" => $createdate, "status" => "paid");
        return $doc;
    }
    /**
     * this function delete a product of given id
     *
     * @param [type] $id
     * @return void
     */
    public function deleteHelperAction($id)
    {

        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->products;
        $success = $collection->deleteOne(array("_id" => new \MongoDB\BSON\ObjectId("$id")));
        return;
    }

    public function addorderAction()
    {

        $data = $this->request->getPost();
        // print_r($data);
        // die();
        $products = $this->getproduct();


        if (isset($data['submit'])) {

            // print_r($data);
            // die();
            $id = $data['id'];
            $quantity = $data['quantity'];
            $email = $data['email'];
            $uname = $data['uname'];
            $token = $data['token'];

            $arr = array("productId" => $id, "quantity" => $quantity, "username" => $uname, "email" => $email, "token" => $token);
            // $header = array("Content-Type" => "application/json");

            $form = [
                'form_params' => $arr
            ];

            $client = new Client();
            $r = $client->request('POST', "192.168.2.24:8080/api/invoices/post?key=$token", $form);

            // print_r($r);
            // die();
        }
        $this->view->message = $products;
    }


    public function displayorderAction()
    {


        $data = $this->request->getPost();
        if (isset($data['status'])) {
            $status = $data['status'];
            $id = $data['orderId'];
            $data = [
                "form" => ["id" => $id, "status" => $status]
            ];
            $header = array('headers' => ['Content-Type' => 'application/x-www-form-urlencoded']);
            $url = "192.168.2.24:8080/api/invoices/update?key=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vZXhhbXBsZS5vcmciLCJhdWQiOiJodHRwOi8vZXhhbXBsZS5jb20iLCJpYXQiOjEzNTY5OTk1MjQsIm5iZiI6MTM1NzAwMDAwMCwicm9sZSI6ImFkbWluIn0.zx9551R5B_KMBtr2KyihvlknTVoPMm2D_Bxss7d-GEs";
            $client = new Client();
            $response = $client->request('PUT', $url, $header, $data);
            echo $response;
            // $data = json_decode($response->getBody());
        } else {

            $url = "192.168.2.24:8080/api/invoices/order?key=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vZXhhbXBsZS5vcmciLCJhdWQiOiJodHRwOi8vZXhhbXBsZS5jb20iLCJpYXQiOjEzNTY5OTk1MjQsIm5iZiI6MTM1NzAwMDAwMCwicm9sZSI6ImFkbWluIn0.zx9551R5B_KMBtr2KyihvlknTVoPMm2D_Bxss7d-GEs";

            $client = new Client();
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody());

            // $data= json_encode($arr);

            // print_r($arr);
            // die();
            $this->view->message = $data;
        }
    }

    public function getproduct()
    {

        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->products;

        $ans = $collection->find([], array("limit" => 50));
        $ans = $ans->toArray();

        return json_decode(json_encode($ans), 1);
        // die;
    }

    public function addproductAction()
    {

        $data = $this->request->getpost();

        if (isset($data['submit'])) {

            // die("Please enter");
            // print_r($data);
            // die;
            $productname = $data['productname'];
            $sku = $data['sku'];
            $price = $data['price'];
            $quantity = $data['quantity'];
            $doc = array(
                "productname" => $data['productname'],
                "sku" => $data['sku'],
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

    public function getHook()
    {
        
    }
}
