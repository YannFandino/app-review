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

// metodo que maneja cada una de las llamadas a la ruta inicial
$app->get('/', function (Request $req, Response $res, array $args) {

    // devolver siempre el objeto $response
    return $this->view->render($res, 'home.phtml', []);;
});

// Categoría
$app->get('/admin/add-category/{name}[/{parent}]', CategoryController::class.":add");
$app->get('/admin/edit-category/{id}/{name}[/{parent}]', CategoryController::class.":update");
$app->get('/admin/list-category', CategoryController::class.":listAll");
?>