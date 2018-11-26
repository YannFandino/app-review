<?php
namespace Classes\controllers;
use Classes\daos\CategoryDao;

class CategoryController {

    public function add($args) {
        $name = mb_strtolower($args['name']);
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
        $id = $args['id'];
        $name = $args['name'];
        $parent = isset($args['parent']) ? $args['parent'] : null;

        $categoryDao = new CategoryDao();
        $result = $categoryDao->updateCategory($id, $name, $parent);

        if (!$result) {
            echo $categoryDao->getError();
        } else {
            echo "Modificado";
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
        $id = $args['id'];

        $categoryDao = new CategoryDao();
        $result = $categoryDao->deleteById($id);

        if (!$result) {
            echo $categoryDao->getError();
        } else {
            echo "Eliminada";
        }
    }

}