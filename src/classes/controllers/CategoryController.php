<?php
namespace Classes\controllers;
use Classes\daos\CategoryDao;

class CategoryController {

    public function add($req, $res, $args) {
        $name = strtolower($args['name']);
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
            $msg = $categoryDao->getError() ? $categoryDao->getError() : "No hay categorias para mostrar";
            echo $msg;
        } else {
            echo "<ul>";
            foreach ($result as $array) {
                $parent = $array['parentInfo'];
                $childs = $array['childs'];
                echo "<strong>{$parent->getName()}</strong>";
                echo "<ul>";
                foreach ($childs as $child) {
                    echo "<li>{$child->getName()}</li>";
                }
                echo "</ul>";
            }
            echo "</ul>";
        }
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