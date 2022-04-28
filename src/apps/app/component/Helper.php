<?php

namespace app\component;

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


use app\component\Myescaper;


class Helper extends Controller
{
    /**
     * this function store user in db and provide token
     *
     * @param [type] $action
     * @param [type] $email
     * @param [type] $password
     * @param [type] $role
     * @param [type] $name
     * @return void
     */
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
    /**
     * this function creates a new doc for storeing DB
     *
     * @param [type] $name
     * @param [type] $email
     * @param [type] $password
     * @param [type] $role
     * @return void
     */
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
    /**
     * this function generate Token and return it
     *
     * @param [type] $name
     * @param [type] $role
     * @return void
     */
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
    /**
     * this function chek role of user and return the role of user
     *
     * @param [type] $email
     * @param [type] $password
     * @return void
     */
    public function checkUser($email, $password)
    {

        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->users;
        $doc = $collection->find(["email" => $email, "password" => $password]);
        // echo "<pre>";
        $arr = $doc->toArray();
        $arr = json_decode(json_encode($arr), 1);
        $role = $arr[0]['role'];
        return $role;
    }

    public function decodeRole($token)
    {

        try {

            $key = "key";
            $decode = JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {

            return "Error decoding";
        }
        $role = $decode->role;
        if ($role == 'admin') {
            return 'admin';
        } else {
            return "xyz";
        }
    }
}
