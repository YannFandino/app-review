<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Classes\controllers\CategoryController;
use Classes\controllers\ProductController;
use Classes\controllers\ReviewController;
use Classes\controllers\HomeController;

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

/**
 * Obtener categorias para ser usadas en el header
 */
$app->add(function(Request $req, Response $res, callable $next) {
    $c = CategoryController::listAll();
    $view = $this->get('view');
    $view->addAttribute('c', $c);
    return $next($req, $res);
});

// Página de inicio
$app->get('/', function (Request $req, Response $res, array $args) {
    $populars = ProductController::getPopular();
    $new = ProductController::getNewOnes();
    return $this->view->render($res, 'home.phtml', ['populars' => $populars, 'new' => $new]);
});

// Login
$app->get('/login', function (Request $req, Response $res, array $args) {
    return $this->view->render($res, '/login.phtml', []);
});
$app->post('/login', HomeController::class.":login");

// Perfil
$app->get('/profile', function (Request $req, Response $res, array $args) {
    if (!isset($_SESSION['user'])) return $res->withRedirect('/', 301);
    return $this->view->render($res, '/profile.phtml', []);
});

// Registro
$app->get('/create-account', function (Request $req, Response $res, array $args) {
    return $this->view->render($res, '/create-account.phtml', []);
});
$app->post('/create-account', HomeController::class.":register");

// Logout
$app->get('/logout', HomeController::class.":logout");

// Detalle de producto
$app->get('/product/{id}', function (Request $req, Response $res, array $args) {
    $product = ProductController::getById($args['id']);
    $reviews = ReviewController::listByProductId($args['id']);
    $args = array("product" => $product, 'reviews' => $reviews);
    return $this->view->render($res, '/details.phtml', $args);
});

// CRUD valoraciones
// Añadir
$app->get('/add-review/{id}', function (Request $req, Response $res, array $args) {
    $product = ProductController::getById($args['id']);
    $reviews = ReviewController::listByProductId($args['id']);
    $args = array("product" => $product, 'reviews' => $reviews);
    return $this->view->render($res, '/add-review.phtml', $args);
});
$app->post('/add-review/{id}', ReviewController::class.":add");

// Modificar
$app->get('/edit-review/{id}', function (Request $req, Response $res, array $args) {
    $product = ProductController::getById($args['id']);
    $review = ReviewController::getByProductAndUser($product->getId(), $_SESSION['user']->getId());
    $viewArgs = array("product"=>$product);
    if ($review) $viewArgs['review'] = $review;
    return $this->view->render($res, '/edit-review.phtml', $viewArgs);
});
$app->post('/edit-review/{id}', ReviewController::class.":update");

// Listar valoraciones por categorias
$app->get('/category/{id}', function (Request $req, Response $res, array $args) {
    $products = ProductController::getByCategory($args['id']);
    return $this->view->render($res, 'list-review.phtml', ['products' => $products]);
});

// Search
$app->get('/search', HomeController::class.':search');
?>