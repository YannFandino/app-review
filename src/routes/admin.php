<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Classes\controllers\CategoryController;
use Classes\controllers\ProductController;
use Classes\controllers\UserController;
use Classes\controllers\AdminController;

// Home
$app->get('/admin/panel', function (Request $req, Response $res, array $args) {
    if (isset($_SESSION['user']) && $_SESSION['user']->getRol() == 1) {
        $products = ProductController::listAll();
        return $this->view->render($res, '/admin/panel-home.phtml', ["products"=>$products]);
    } else {
        return $res->withRedirect('/', 301);
    }
});

// Usuario
$app->get('/admin/userByEmail/{email}', UserController::class.":getByEmail");
$app->get('/admin/userByUsername/{username}', UserController::class.":getByUsername");
$app->get('/admin/userByRole/{role_id}', UserController::class.":getByRole");
$app->post('/admin/edit-user', UserController::class.":update");
$app->post('/admin/edit-password', UserController::class.":update");
$app->post('/admin/delete-user/{id}', UserController::class.":delete");

// CRUD Categoría
$app->get('/admin/categories', function (Request $req, Response $res, array $args) {
    $categoryController = new CategoryController();
    $categories = $categoryController->listAll();
    $args = array("categories" => $categories);
    return $this->view->render($res, '/admin/categories.phtml', $args);
});
$app->post('/admin/categories', AdminController::class.":addCategory");
$app->post('/admin/edit-category', CategoryController::class.":update");
$app->post('/admin/delete-category', CategoryController::class.":delete");

// CRUD productos
$app->get('/admin/products', function (Request $req, Response $res, array $args) {
    $categories = CategoryController::listAll();
    $args = array("categories" => $categories);
    return $this->view->render($res, '/admin/products.phtml', $args);
});
$app->post('/admin/products', AdminController::class.":addProduct");
$app->post('/admin/edit-product/{id}/{name}/{description}/{details}/{category}', ProductController::class.":update");
$app->post('/admin/delete-product', ProductController::class.":delete");
?>