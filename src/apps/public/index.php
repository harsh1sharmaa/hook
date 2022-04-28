<?php
// (new \Phalcon\Debug())->listen();

// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
$_SERVER['REQUEST_URI'] = str_replace("/apps/", "/", $_SERVER['REQUEST_URI']);


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
use app\component\Hook;






require_once('../vendor/autoload.php');
$profiler = new \Fabfuel\Prophiler\Profiler();
$toolbar = new \Fabfuel\Prophiler\Toolbar($profiler);
$toolbar->addDataCollector(new \Fabfuel\Prophiler\DataCollector\Request());
// echo $toolbar->render();


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
        'App\Listeners' => APP_PATH . '/listeners'
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

$container->set(
    'hook',
    function () {

        return new Hook();
    }
);
$container->set(
    'config',
    function () {
        $configData = require '../app/config/config.php';

        return new Config($configData);
    }
);

// $container->set(

//     "tool",
//     $toolbar

// );

// Simple database connection to localhost

// Connecting to a domain socket, falling back to localhost connection

$DBinfo = $container->get('config')->DBdata;

$container->set(
    'mongo',
    function () use ($DBinfo) {
        $mongo = new \MongoDB\Client("mongodb://mongo", array("username" => $DBinfo->username, "password" => $DBinfo->password));

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


$container->set(
    'myescaper',
    function () {

        $fileName = APP_PATH . '/component/myescaper.php';
        $factory  = new ConfigFactory();

        $myescaper = $factory->newInstance('php', $fileName);
        return $myescaper;
    }
);

$container->set(
    'db',
    function () {

        $m = $this->mongo;
        $db = $m->store;
        return $db;
    }
);

$adapter = new ls('../app/log/newlog.log');

$logger  = new Logger(
    'messages',
    [
        'main' => $adapter,
    ]
);

$container->set(
    'logger',
    $logger

);




// $eventsManager->attach(
//     'notification:afterUpdate',
//     new \App\Listeners\NotificationListeners()
// );
$eventsManager->attach(
    'db:afterdb',
    new \App\Listeners\NotificationListeners()
);
$eventsManager->attach(
    'notification:afterUpdate',
    new \App\Listeners\NotificationListeners()
);
$eventsManager->attach(
    'notification:editproduct',
    new \App\Listeners\NotificationListeners()
);
$eventsManager->attach(
    'notification:updateQuantity',
    new \App\Listeners\NotificationListeners()
);


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
