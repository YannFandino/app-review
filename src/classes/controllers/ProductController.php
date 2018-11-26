<?php
namespace Classes\controllers;
use Classes\daos\ProductDao;

class ProductController {

    public function add($args) {
        $name = mb_strtolower($args['name']);
        $description = mb_strtolower($args['description']);
        $details = isset($args['details']) ? $args['details'] : null;
        $category = $args['category'];

        $productDao = new ProductDao();
        $result = $productDao->addProduct($name, $description, $details, $category);

        if (!$result) {
            echo $productDao->getError();
        } else {
            echo "Insertado";
        }
    }

    public function update($req, $res, $args) {
        $id = $args['id'];
        $name = strtolower($args['name']);
        $description = isset($args['description']) ? $args['description'] : null;
        $details = isset($args['details']) ? $args['details'] : null;
        $category = isset($args['category']) ? $args['category'] : null;

        $productDao = new ProductDao();
        $result = $productDao->updateProduct($name, $description, $details, $category);

        if (!$result) {
            echo $productDao->getError();
        } else {
            echo "Modificado";
        }
    }

    public function listAll() {
        $productDao = new ProductDao();
        $result = $productDao->getAll();

        if (empty($result)) {
            $msg = $productDao->getError() ? $productDao->getError() : "No hay productos para mostrar";
            return array("error" => $msg);
        } else {
            return $result;
        }
    }

    public function delete($req, $res, $args) {
        $id = $args['id'];

        $productDao = new ProductDao();
        $result = $productDao->deleteById($id);

        if (!$result) {
            echo $productDao->getError();
        } else {
            echo "Eliminado";
        }
    }
}