<?php
/*
 * Rutas de la aplicación
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Classes\controllers\CategoryController;

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

// Home - Página de inicio
$app->get('/', function (Request $req, Response $res, array $args) {
    return $this->view->render($res, 'home.phtml', []);;
});

// Categoría
// Añadir categoría
$app->get('/admin/add-category/{name}[/{parent}]', CategoryController::class.":add");
$app->get('/admin/edit-category/{id}/{name}[/{parent}]', CategoryController::class.":update");
$app->get('/admin/list-category', CategoryController::class.":listAll");
$app->get('/admin/delete-category/{id}', CategoryController::class.":delete");
?>