<?php

use Phalcon\Mvc\Micro;
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use App\middle;

use Phalcon\Config;


// require_once('./vendor/autoload.php');
require_once('./vendor/autoload.php');

$loader = new Loader();
$container = new FactoryDefault();

$loader->registerDirs(
    [

        "./controller",


    ]
);


$loader->registerNamespaces(
    [
        'Api\Handlers' => './handlers',
        'App\middle' => './middlle',
        'app\admin\Controllers' => './controllers',

    ]
);

//**********************************************set config file om container **************************** */
$container->set(
    'config',
    function () {
        $configData = require './config/config.php';

        return new Config($configData);
    }
);

/**************************************SET MONGO DB ************************************************ */

$DBinfo = $container->get('config')->DBdata;
$container->set(
    'mongo',
    function () use ($DBinfo) {

        $mongo = new \MongoDB\Client("mongodb://mongo", array("username" => $DBinfo->username, "password" => $DBinfo->password));

        return $mongo;
    },
    true
);





$container->set('view', function () {
    $view = new \Phalcon\Mvc\View\Simple();
    $view->setViewsDir('../frontend/view');
    return $view;
}, true);

$application = new Application($container);

//********************************set container for micro Application ************************************************* */

$loader->register();
$prod = new Api\Handlers\Product();
$app = new Micro($container);
$path = $container->get('config')->path;

$app->get(
    $path->api,
    [
        $prod,
        'allproducts'
    ]

);
$app->get(
    $path->api . '/{page}/{limit}',
    [
        $prod,
        'jumpToPage'
    ]

);
$app->get(
    $path->api . '/search/{name}',
    [
        $prod,
        'searchByName'
    ]

);
$app->get(
    $path->api . '/search/{name}/{limit}',
    [
        $prod,
        'searchByNameAndLimit'
    ]

);

//******************************************************************************************************************************** */
$app->get(
    '/api/api',
    [
        $prod,
        'get'
    ]
);
$app->get(
    '/api/invoices/get',
    [
        $prod,
        'fun'
    ]
);
$app->get(
    '/api/invoices/search/{name}',
    [
        $prod,
        'search'
    ]
);
$app->get(
    '/api/invoices/get/{limit}',
    [
        $prod,
        'limit'
    ]
);
$app->get(
    '/api/invoices/get/{limit}/{pageno}',
    [
        $prod,
        'skip'
    ]
);


$app->get(
    '/api/invoices/test',
    [
        $prod,
        'test'
    ]
);
$app->get(
    '/api/invoices/order',
    [
        $prod,
        'getorder'
    ]
);

$app->post(
    '/api/invoices/post',
    [
        $prod,
        'postdata'
    ]
);
$app->put(
    '/api/invoices/update/status',
    [
        $prod,
        'update'
    ]
);
$app->notFound(
    function () use ($app) {
        echo 'Nothing to see here. Move along....';
        die;
    }
);
$app->before(
    function () use ($app) {
        $midobj = new App\middle\FirewallMiddleware();
        if ($_SERVER['REQUEST_URI'] == '/api') {
            return true;
        }
        $key = $app->request->get('key');
        if ($key == 'create') {
            $midobj = new App\middle\FirewallMiddleware();
            $midobj->createToken();
            return false;
        }
        if (isset($key) && $key !== '') {

            $midobj = new App\middle\FirewallMiddleware();

            if ($midobj->checktoken($key)) {

                return true;
            } else {
                return "invalid user token";
            }
        } else {

            return "token in not provided";
        }
    }

);



$app->handle(
    $_SERVER['REQUEST_URI']
);
