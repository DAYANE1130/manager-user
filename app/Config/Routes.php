<?php


namespace Config;


$routes = Services::routes();

if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
  require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
// $routes->setTranslateURIDashes(false);
$routes->set404Override();
//$routes->setIgnoreCaseSensitive(true);
$routes->setAutoRoute(true);

// mudar as rotas
// $routes->get('/', 'Home::index');
// $routes->post('auth', 'Auth::register'); // assim funciona
//$routes->post('auth/register', 'AuthController::getRegister');

// $routes->post('login', 'AuthController::login');


//$routes->match(['get', 'post'], 'auth/register', 'Auth::register');



if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
  require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
