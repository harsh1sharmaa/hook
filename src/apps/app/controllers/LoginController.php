<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use app\component\Myescaper;
use app\component;


/**
 * Undocumented class
 */
class LoginController extends Controller
{

    /**
     * this function register user and provide access token
     *
     * @return void
     */
    public function registerAction()
    {
        $data = $this->request->getPost();

        if (isset($data['submit'])) {


            $name = $data['name'];
            $email = $data['email'];
            $password = $data['password'];
            $role = $data['role'];
            if ($name != "" && $email != "" && $password != "" && $role != "") {
                $logger = $this->logger;
                $logger->info("Registering user" . $name);

                $obj = new app\component\Helper();
                $token = $obj->storeUser("register", $email, $password, $role, $name);
                // $this->view->message = $token;
                $this->response->redirect("apps/login/register?key=$token");
            }
        }
    }
    /**
     * this function give access to admin of this app
     *
     * @return void
     */
    public function loginAction()
    {
        $data = $this->request->getPost();

        if (isset($data['submit'])) {


            $email = $data['email'];
            $password = $data['password'];

            // echo "<pre>";
            // print_r($data);
            // die;

            if ($email != "" && $password != "") {

                $obj = new app\component\Helper();
                $role = $obj->checkUser($email, $password);
            }

            $admin = $this->config->admin;

            if ($role == $admin->role) {

                $this->response->redirect("/apps/hook/displayproducts");
            }
        }
    }

    public function tokenAction()
    {
    }

    public function admindashAction()
    {
    }
}
// eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vZXhhbXBsZS5vcmciLCJhdWQiOiJodHRwOi8vZXhhbXBsZS5jb20iLCJpYXQiOjEzNTY5OTk1MjQsIm5iZiI6MTM1NzAwMDAwMCwicm9sZSI6ImN1c3RvbWVyIiwibmFtZSI6ImFkZHUifQ.iv5o1rGnfQwF8o0tTWtMgfuWDnA92plEK4MXJbkGnsg
