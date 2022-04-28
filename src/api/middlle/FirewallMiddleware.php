<?php

namespace App\middle;

use Phalcon\Events\Event;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Phalcon\Mvc\Controller;

/**
 * FirewallMiddleware
 *
 * @property Request  $request
 * @property Response $response
 */
class FirewallMiddleware extends Controller
{

    public function checktoken($token)
    {
      
        try {

            $key = "key";
            $decode = JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {

            return "Error decoding";
            
        }
        $role = $decode->role;
        if ($role == 'admin') {
            return true;
        } else {
            return false;
        }
    }

    public function createToken()
    {

        // echo "Creating";
        $role = $this->request->get('role');
        if (!isset($role) && $role = '') {
            echo "Plz provides a role";
            die();
        }
        $fname = $this->request->get('fname');
        $lname = $this->request->get('lname');
        $this->addUser($fname, $lname, $role);
        $key = "key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000,
            "role" => $role
        );
        $jwt = JWT::encode($payload, $key, 'HS256');
        echo $jwt;
        die();
    }

    public function displayApi()
    {

        $str = '<form action="api/invoices/newuser" method="get">
        <label for="fname">First name:</label><br>
        <input type="text" id="fname" name="fname"><br>
        <label for="lname">Last name:</label><br>
        <input type="text" id="lname" name="lname"><br>
        <label for="lname"> role:</label><br>
        <input type="text" id="lname" name="role"><br>
        <input type="hidden" id="lname" name="key" value="create"><br>
        <input type="submit" id="lname" name="submit" value="submit">
      </form><br>
      for  products : <strong>/invoices/get?key={TOKEN}</strong>
      <br>
      for  search product : <strong>/invoices/search/{name}?key={TOKEN}</strong>
      <br>
      for product in limit : <strong>/invoices/get/{limit}?key={TOKEN}</strong>
      <br>
      for jump to  nth page : <strong>/invoices/get/{limit}/{pageno}?key={TOKEN}</strong><br><br>
       ';
        echo $str;
        // die(); 
    }

    public function addUser($fname, $lname, $role)
    {


        $m = $this->mongo;
        $db = $m->store;
        $collection = $db->user;

        $collection->insertOne(array("fname" => $fname, "lname" => $lname, "role" => $role));
        return;
    }

    /**
     * @param Event $event
     * @param Micro $application
     *
     * @returns bool
     */
    // public function beforeHandleRoute(
    //     Event $event,
    //     Micro $application
    // ) {
    //     $whitelist = [
    //         '10.4.6.1',
    //         '10.4.6.2',
    //         '10.4.6.3',
    //         '10.4.6.4',
    //     ];

    //     $ipAddress = $application
    //         ->request
    //         ->getClientAddress();

    //     if (true !== array_key_exists($ipAddress, $whitelist)) {
    //         $this
    //             ->response
    //             ->redirect('/401')
    //             ->send();

    //         return false;
    //     }

    //     return true;
    // }

    // /**
    //  * @param Micro $application
    //  *
    //  * @returns bool
    //  */
    // public function call(Micro $application)
    // {
    //     return true;
    // }
}
