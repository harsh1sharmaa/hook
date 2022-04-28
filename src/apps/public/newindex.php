<?php

// use Phalcon\Loader;
// use Phalcon\Di\FactoryDefault;
// use Phalcon\Mvc\Application
// use Phalcon\Mvc\View;

// use Phalcon\Url;

// define('BASE_PATH', dirname(__DIR__));
// define('APP_PATH', BASE_PATH . '/app');

// $loader = new Loader();

// $loader->registerDirs(
//     [
//         APP_PATH . '/controllers/',
//         APP_PATH . '/models/',
//     ]
// );

// // Create a DI
// $container = new FactoryDefault();


// // ...

// $container->set(
//     'view',
//     function () {
//         $view = new View();
//         $view->setViewsDir(APP_PATH . '/views/');

//         return $view;
//     }
// );





// // ...

// $container->set(
//     'url',
//     function () {
//         $url = new Url();
//         $url->setBaseUri('/');

//         return $url;
//     }
// );



// $loader->register();


// $application = new Application($container);
// try {
//     // Handle the request
//     $response = $application->handle(
//         $_SERVER["REQUEST_URI"]
//     );

//     $response->send();
// } catch (\Exception $e) {
//     echo 'Exception: ', $e->getMessage();
// }

?>
