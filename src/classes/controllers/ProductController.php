<?php
namespace Classes\controllers;
use Classes\daos\ProductDao;
use Slim\Http\UploadedFile;

class ProductController {

    const DIR = __DIR__ . "/../../../public/img/products/";

    public function add($args) {
        $name = mb_strtolower($args['name']);
        $description = mb_strtolower($args['description']);
        $details = isset($args['details']) ? $args['details'] : null;
        $category = $args['category'];
        $imgs = $args['imgs'];

        $productDao = new ProductDao();
        $idProduct = $productDao->addProduct($name, $description, $details, $category);

        if (!$idProduct) {
            return array('error' => $productDao->getError());
        }

        $directory = self::DIR . $idProduct;
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        $filenames = $this->moveAndNameFile($directory, $name, $imgs);
        $addImages = $productDao->addImages($idProduct, $filenames);

        if (!$addImages) {
            return array('error' => $productDao->getError());
        }
        return true;
    }

    public function update($req, $res, $args) {
        $id = $args['id'];
        $name = strtolower($args['name']);
        $description = isset($args['description']) ? $args['description'] : null;
        $details = isset($args['details']) ? $args['details'] : null;
        $category = isset($args['category']) ? $args['category'] : null;

        $productDao = new ProductDao();
        $result = $productDao->updateProduct($id, $name, $description, $details, $category);

        if (!$result) {
            echo $productDao->getError();
        } else {
            echo "Modificado";
        }
    }

    public static function listAll() {
        $productDao = new ProductDao();
        $result = $productDao->getAll();

        if (empty($result)) {
            $msg = $productDao->getError() ? $productDao->getError() : "No hay productos para mostrar";
            return array("error" => $msg);
        } else {
            return $result;
        }
    }

    public static function getPopular() {
        $productDao = new ProductDao();
        $result = $productDao->getPopular();

        if (empty($result)) {
            $msg = $productDao->getError() ? $productDao->getError() : "No hay productos para mostrar";
            return array("error" => $msg);
        } else {
            return $result;
        }
    }

    public static function getNewOnes() {
        $productDao = new ProductDao();
        $result = $productDao->getNewOnes();

        if (empty($result)) {
            $msg = $productDao->getError() ? $productDao->getError() : "No hay productos para mostrar";
            return array("error" => $msg);
        } else {
            return $result;
        }
    }

    public static function getById($id) {
        $productDao = new ProductDao();
        $product = $productDao->getById($id);

        if (!$product) {
            return false;
        }
        return $product;
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

    function moveAndNameFile($directory, $productName, $imgs) {
        $filenames = array();
        $basename = md5($productName);
        foreach ($imgs as $key=>$img) {
            var_dump($img);
            $extension = pathinfo($img->getClientFilename(), PATHINFO_EXTENSION);
            $filename = sprintf('%s.%0.8s', "$key-$basename" , $extension);
            $img->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
            array_push($filenames, $filename);
        }

        return $filenames;
    }
}