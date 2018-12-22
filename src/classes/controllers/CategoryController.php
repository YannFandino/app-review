<?php
namespace Classes\controllers;
use Classes\daos\CategoryDao;

class CategoryController {

    public function add($args) {
        $name = mb_strtolower(trim($args['name']));
        $parent = isset($args['parent']) ? ($args['parent'] == 0 ? null : $args['parent']) : null;

        $categoryDao = new CategoryDao();
        $result = $categoryDao->addCategory($name, $parent);

        if (!$result) {
            return array('error' => $categoryDao->getError());
        } else {
            return true;
        }
    }

    public function update($req, $res, $args) {
        $id = $req->getParam('id');
        $name = mb_strtolower($req->getParam('name'));

        $categoryDao = new CategoryDao();
        $result = $categoryDao->updateCategory($id, $name);

        if (!$result) {
            return $res->withHeader("Content-Type:", "text/html")
                ->withStatus(400)
                ->write($categoryDao->getError());
        } else {
            return $res->withHeader("Content-Type:", "text/html")
                ->withStatus(200)
                ->write("Categoría modificada correctamente");
        }
    }

    public static function listAll() {
        $categoryDao = new CategoryDao();
        $result = $categoryDao->getAll();

        if (empty($result)) {
            $msg = $categoryDao->getError() ? $categoryDao->getError() : "No hay categorías para mostrar";
            return array("error" => $msg);
        } else {
            return $result;
        }
    }

    public function listAllParents() {
        $categoryDao = new CategoryDao();
        $result = $categoryDao->getAllParents();

        if (empty($result)) {
            return false;
        }
        return $result;
    }

    public function delete($req, $res, $args) {
        $id = $req->getParam('id');

        $categoryDao = new CategoryDao();
        $result = $categoryDao->deleteById($id);

        if (!$result) {
            return $res->withHeader("Content-Type:", "text/html")
                            ->withStatus(400)
                            ->write($categoryDao->getError());
        } else {
            return $res->withHeader("Content-Type:", "text/html")
                            ->withStatus(200)
                            ->write("Eliminada correctamente");
        }
    }

}