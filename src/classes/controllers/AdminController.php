<?php
namespace Classes\controllers;
use Classes\controllers\CategoryController;
use Classes\controllers\ProductController;

class AdminController {

    private $view;

    /**
     * HomeController constructor.
     * @param $view
     */
    public function __construct($c) {
        $this->view = $c['view'];
    }

    public function addCategory($req, $res, $args) {
        $args = array('name' => trim($req->getParam('name')),
                      'parent' => $req->getParam('parent'));
        $categoryController = new CategoryController();
        $result = $categoryController->add($args);

        if (isset($result['error'])) {
            $_SESSION['add-error'] = $result['error'];
        }
        return $res->withRedirect('/app-review/admin/categories', 301);
    }

    public function addProduct($req, $res, $args) {
        $imgs = $req->getUploadedFiles()['img'];
        $isImgsOk = self::checkImages($imgs);

        $args = array('name' => trim($req->getParam('name')),
                      'description' => trim($req->getParam('description')),
                      'details' => trim($req->getParam('details')),
                      'category' => $req->getParam('category'),
                      'imgs' => $imgs);

        if (isset($isImgsOk['error'])) {
            $_SESSION['add-error'] = $isImgsOk['error'];
            $_SESSION['args'] = $args;
            return $res->withRedirect('/app-review/admin/products', 301);
        }

        $productController = new ProductController();
        $result = $productController->add($args);

        if (isset($result['error'])) {
            $_SESSION['add-error'] = $result['error'];
            $_SESSION['args'] = $args;
            return $res->withRedirect('/app-review/admin/products', 301);
        }
        return $res->withRedirect('/app-review/admin/products', 301);
    }

    public function addReview($req, $res, $args) {
        $args = array('product' => trim($req->getParam('product')),
                      'user' => trim($req->getParam('user')),
                      'points' => $req->getParam('points'),
                      'comment' => trim($req->getParam('comment')),
                      'date_created' => $req->getParam('date_created'),
                      'last_modified' => $req->getParam('last_modified'),
                      'is_approved' => $req->getParam('is_approved'));
        
        $reviewController = new ReviewController();
        $result = $reviewController->add($args);

        if (isset($result['error'])) {
            $_SESSION['add-error'] = $result['error'];
        }
        return $res->withRedirect('/app-review/admin/categories', 301);
    }

    public function checkImages(array $imgs) {
        if (count($imgs) > 5) {
            return array("error" => "El número máximo de imagenes es 5");
        }
        foreach ($imgs as $img) {
            if ($img->getError() === UPLOAD_ERR_NO_FILE) {
                return array("error" => "Debe subir al menos una imagen");
            }
            if ($img->getSize() > 2097152) {
                return array("error" => "El tamaño de la imagen no puede exceder de 2MB");
            }
            if (!in_array($img->getClientMediaType(), ['image/png', 'image/jpeg'])) {
                return array("error" => "Solo se pueden subir ficheros con extensión .png y .jpg");
            }
            if ($img->getError() !== UPLOAD_ERR_OK) {
                return array("error" => "Ha ocurrido un error al intentar subir la imagen");
            }
        }
        return true;
    }

}