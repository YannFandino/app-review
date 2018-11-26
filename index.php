<?php
/*
** Configuracion general e inicializacion **
*/

require './vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = 'localhost';
$config['db']['user']   = 'root';
$config['db']['pass']   = '';
$config['db']['dbname'] = 'app_review';

$app = new \Slim\App(['settings' => $config]);

// Se crea un container que servira para añadir mas 
// dependencias al proyecto y enlazarlas con el framework.
$container = $app->getContainer();

// Añadimos nueva depenencias para renderizar plantillas .phtml
$container['view'] = new \Slim\Views\PhpRenderer('./src/views/');

// Importamos el resto de codigo de nuestra aplicacion
require './src/routes/route.php';
require './src/db/DBConnection.php';
require './src/classes/models/Category.php';
require './src/classes/models/Product.php';
require './src/classes/models/Review.php';
require './src/classes/models/User.php';
require './src/classes/daos/CategoryDao.php';
require './src/classes/daos/ProductDao.php';
require './src/classes/daos/ReviewDao.php';
require './src/classes/daos/UserDao.php';
require './src/classes/controllers/CategoryController.php';
require './src/classes/controllers/ProductController.php';
require './src/classes/controllers/ReviewController.php';
require './src/classes/controllers/UserController.php';


require './src/classes/controllers/HomeController.php';
require './src/classes/controllers/AdminController.php';

session_start();
// Se lanza toda la aplicacion.
$app->run();
?>