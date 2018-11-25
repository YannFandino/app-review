<?php
namespace Classes\controllers;
use Classes\daos\ProductDao;

class ProductController {

    public function add($req, $res, $args) {
        $name = strtolower($args['name']);
        $description = isset($args['description']) ? $args['description'] : null;
        $details = isset($args['details']) ? $args['details'] : null;
        $category = isset($args['category']) ? $args['category'] : null;

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

    public function listAll($req, $res, $args) {
        $productDao = new ProductDao();
        $result = $productDao->getAll();

        if (empty($result)) {
            $msg = $productDao->getError() ? $productDao->getError() : "No hay productos para mostrar";
            echo $msg;
        } else {
            echo "<ul>";
            foreach ($result as $arrayProduct) {
                echo "<strong>{$arrayProduct->getName()}</strong>";
                echo "<ul>";
                echo "<li>{$arrayProduct->getName()}</li>";
                echo "<li>{$arrayProduct->getDescription()}</li>";
                echo "<li>{$arrayProduct->getDetails()}</li>";
                echo "<li>{$arrayProduct->getCategory()}</li>";
                echo "</ul>";
            }
            echo "</ul>";
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