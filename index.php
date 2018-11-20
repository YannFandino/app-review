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

// Añadimos función que devuelve conexión con BBDD
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// Importamos el resto de codigo de nuestra aplicacion
require './src/routes/route.php';
require './src/db/DBConnection.php';
require './src/classes/models/Category.php';
require './src/classes/models/Product.php';
require './src/classes/models/Review.php';
require './src/classes/models/User.php';
require './src/classes/daos/CategoryDao.php';
require './src/classes/controllers/CategoryController.php';

session_start();
// Se lanza toda la aplicacion.
$app->run();
?>