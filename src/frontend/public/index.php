<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
$_SERVER['REQUEST_URI'] = str_replace("/frontend/", "/", $_SERVER['REQUEST_URI']);

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;

use Phalcon\Config\ConfigFactory;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream as ls;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
// use app\component\Locale;
use Phalcon\Mvc\Router;
use app\component\Locale;





require_once('../vendor/autoload.php');


$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();
$eventsManager = new EventsManager();
$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/component/",

    ]
);



$loader->registerNamespaces(
    [

        'app\component' => APP_PATH . '/component',
        // 'app\component' => APP_PATH . '/component',

    ]
);


$loader->register();

//************************************set view in di*********************************************

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

// Simple database connection to localhost

// Connecting to a domain socket, falling back to localhost connection

$container->set(
    'mongo',
    function () {
        $mongo = new \MongoDB\Client("mongodb://mongo", array("username" => 'root', "password" => "password123"));

        return $mongo;
    },
    true
);


//************************************set base url in di*********************************************


$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;
    }
);


// $container->set(
//     'myescaper',
//     function () {

//         $fileName = APP_PATH .'/component/myescaper.php';
//         $factory  = new ConfigFactory();

//         $myescaper = $factory->newInstance('php', $fileName);
//         return $myescaper;
//     }
// );

$container->set(
    'EventsManager',
    $eventsManager
);



$application = new Application($container);
$application->setEventsManager($eventsManager);

$container->set('locale', (new Locale())->getTranslator());




//***************************************router***************************************************** */

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e;
}
