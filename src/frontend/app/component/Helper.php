<?php

namespace app\component;

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


use app\component\Myescaper;


class Helper extends Controller
{

    public function storeUser($action,  $email, $password, $role, $name)
    {


        $m = $this->mongo;

        $db = $m->store;

        $collection = $db->users;

        switch ($action) {

            case 'register':

                $doc = $this->createdoc($name, $email, $password, $role);
                $collection->insertOne($doc);

                $token = $this->generateToken($name, $role);
                return $token;



                break;

            case 'login':

                break;
        }
    }

    public function createdoc($name, $email, $password, $role)
    {
        $escp = new Myescaper();
        $name = $escp->sanitize($name);
        $email = $escp->sanitize($email);
        $password = $escp->sanitize($password);
        $role = $escp->sanitize($role);

        $doc = array("name" => $name, "email" => $email, "password" => $password, "role" => $role);
        return $doc;
    }

    public function generateToken($name, $role)
    {

        $key = "key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000,
            "role" => $role,
            "name" => $name
        );
        $jwt = JWT::encode($payload, $key, 'HS256');

        return $jwt;
    }

    public function checkUser($email, $password)
    {
        // echo $email;
        // echo $password;
        // die();

        $m = $this->mongo;

        $db = $m->store;

        $collection = $db->users;

        // $doc = $collection->findOne(array('$and'=>[["email"=>$email],["password"=>$password]]));
        $doc = $collection->find(["email" => $email, "password" => $password]);

        echo "<pre>";
        $arr = $doc->toArray();
        $arr=json_decode(json_encode($arr), 1);
        // print_r(json_decode(json_encode($arr), 1));
        $role=$arr[0]['role'];
        // echo $role;
        // die();
        return $role;
    }
}
