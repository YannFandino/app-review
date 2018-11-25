<?php
namespace Classes\controllers;
use Classes\controllers\CategoryController;

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
        return $res->withRedirect('/app-review/admin/add-category', 301);
    }
}