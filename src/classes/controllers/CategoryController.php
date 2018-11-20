<?php
namespace Classes\controllers;
use Classes\daos\CategoryDao;

class CategoryController {

    public function add($req, $res, $args) {
        $name = $args['name'];
        $parent = isset($args['parent']) ? $args['parent'] : null;

        $categoryDao = new CategoryDao();
        $result = $categoryDao->addCategory($name, $parent);

        if (!$result) {
            echo $categoryDao->getError();
        } else {
            echo "Insertado";
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

    public function listAll($req, $res, $args) {
        $categoryDao = new CategoryDao();
        $result = $categoryDao->getAll();

        if (empty($result)) {
            echo $categoryDao->getError();
        } else {
            foreach ($result as $array) {
                var_dump($array);
            }
        }
    }

}