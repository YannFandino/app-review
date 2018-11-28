<?php
/*
 * Rutas de la aplicación
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Classes\controllers\CategoryController;
use Classes\controllers\ProductController;
use Classes\controllers\ReviewController;
use Classes\controllers\UserController;
use Classes\controllers\HomeController;
use Classes\controllers\AdminController;

$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));

        if($request->getMethod() == 'GET') {
            return $response->withRedirect((string)$uri, 301);
        }
        else {
            return $next($request->withUri($uri), $response);
        }
    }

    return $next($request, $response);
});

// Categoría
$app->get('/admin/edit-category/{id}/{name}[/{parent}]', CategoryController::class.":update");
$app->get('/admin/delete-category/{id}', CategoryController::class.":delete");

// Usuario
$app->get('/admin/userByEmail/{email}', UserController::class.":getByEmail");
$app->get('/admin/userByUsername/{username}', UserController::class.":getByUsername");
$app->get('/admin/userByRole/{role_id}', UserController::class.":getByRole");
$app->get('/admin/edit-user/{id}/{name}/{email}/{password}/{role_id}', UserController::class.":update");
$app->get('/admin/delete-user/{id}', UserController::class.":delete");

// Producto
$app->get('/admin/edit-product/{id}/{name}/{description}/{details}/{category}', ProductController::class.":update");
$app->get('/admin/delete-product/{id}', ProductController::class.":delete");

// Home
// Página de inicio
$app->get('/', function (Request $req, Response $res, array $args) {
    $products = ProductController::listAll();
    return $this->view->render($res, 'home.phtml', ['products' => $products]);
});

// Admin Panel
// Home
$app->get('/admin/panel', function (Request $req, Response $res, array $args) {
    if (isset($_SESSION['user']) && $_SESSION['user']->getRol() == 1) {
        return $this->view->render($res, '/admin/panel-home.phtml', []);
    } else {
        return $res->withRedirect('/app-review', 301);
    }
});
// CRUD Categoría
$app->get('/admin/categories', function (Request $req, Response $res, array $args) {
    $categoryController = new CategoryController();
    $categories = $categoryController->listAll();
    $args = array("categories" => $categories);
    return $this->view->render($res, '/admin/categories.phtml', $args);
});
$app->post('/admin/categories', AdminController::class.":addCategory");

// CRUD productos
$app->get('/admin/products', function (Request $req, Response $res, array $args) {
    $products = ProductController::listAll();
    $categories = CategoryController::listAll();
    $args = array("products" => $products,
                  "categories" => $categories);
    return $this->view->render($res, '/admin/products.phtml', $args);
});
$app->post('/admin/products', AdminController::class.":addProduct");

// CRUD valoraciones
// Añadir
$app->get('/add-review/{id}', function (Request $req, Response $res, array $args) {
    $product = ProductController::getById($args['id']);
    return $this->view->render($res, '/add-review.phtml', ["product" => $product]);
});
$app->post('/add-review/{id}', ReviewController::class.":add");
// Listar
$app->get('/list-review/{id}', function (Request $req, Response $res, array $args) {
    $product = ProductController::getById($args['id']);
    $reviews = ReviewController::listById($args['id']);
    return $this->view->render($res, '/list-review.phtml', ["product" => $product, 'reviews' => $reviews]);
});
// Modificar
$app->get('/edit-review/{id}', function (Request $req, Response $res, array $args) {
    $product = ProductController::getById($args['id']);
    $review = ReviewController::getByProductAndUser($product->getId(), $_SESSION['user']->getId());
    $viewArgs = array("product"=>$product);
    if ($review) $viewArgs['review'] = $review;
    return $this->view->render($res, '/edit-review.phtml', $viewArgs);
});
$app->post('/edit-review/{id}', ReviewController::class.":update");

// Login
$app->get('/login', function (Request $req, Response $res, array $args) {
    return $this->view->render($res, '/login.phtml', []);
});
$app->post('/login', HomeController::class.":login");
// Registro
$app->get('/create-account', function (Request $req, Response $res, array $args) {
    return $this->view->render($res, '/create-account.phtml', []);
});
$app->post('/create-account', HomeController::class.":register");
// Logout
$app->get('/logout', HomeController::class.":logout");
?>