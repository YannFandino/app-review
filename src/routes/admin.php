<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Classes\controllers\CategoryController;
use Classes\controllers\ProductController;
use Classes\controllers\ReviewController;
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
$app->get('/admin/edit-product/{id}', function (Request $req, Response $res, array $args) {
    $categories = CategoryController::listAll();
    $product = ProductController::getById($args['id']);
    $args = array("categories" => $categories, "product" => $product);
    if (!$product) throw new \Slim\Exception\NotFoundException($req, $res);
    return $this->view->render($res, '/admin/edit-product.phtml', $args);
});
$app->post('/admin/products', AdminController::class.":addProduct");
$app->post('/admin/edit-product', ProductController::class.":update");
$app->post('/admin/edit-img', ProductController::class.":updateImg");
$app->post('/admin/delete-product', ProductController::class.":delete");

// Valoraciones
// Listar valoraciones por categorias
$app->get('/admin/reviews', function (Request $req, Response $res, array $args) {
    $reviews = ReviewController::listPending();
    return $this->view->render($res, '/admin/reviews.phtml', ['reviews' => $reviews]);
});
$app->get('/admin/reviews/all', function (Request $req, Response $res, array $args) {
    $reviews = ReviewController::listAll();
    return $this->view->render($res, '/admin/all-reviews.phtml', ['reviews' => $reviews]);
});
$app->get('/admin/review/{id}', function (Request $req, Response $res, array $args) {
    $review = ReviewController::getById($args['id']);
    return $this->view->render($res, '/admin/moderate.phtml', ['review' => $review]);
});
$app->post('/admin/review/{id}', function (Request $req, Response $res, array $args) {
    $review = ReviewController::getById($args['id']);
    $isOk = ReviewController::approve($args['id']);
    if (!$isOk)
        $msg = "Ha ocurrido un error";
    else
        $msg = "Valoración aprovada";

    return $this->view->render($res, '/admin/moderate.phtml', ['review' => $review, "msg" => $msg]);
});

$app->post('/admin/del-review', ReviewController::class.":delete");
?>