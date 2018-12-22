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
        $id = $req->getParam('id');
        $name = mb_strtolower($req->getParam('name'));
        $description = mb_strtolower($req->getParam('description'));
        $details = mb_strtolower($req->getParam('details'));
        $category = $req->getParam('category');

        $productDao = new ProductDao();
        $result = $productDao->updateProduct($id, $name, $description, $details, $category);

        if (!$result) {
            $_SESSION['msg'] = array("type" => "error", "msg" => $productDao->getError());
            return $res->withRedirect("/admin/edit-product/{$id}", 301);
        } else {

            $_SESSION['msg'] = array("type" => "success", "msg" => 'Producto modificado');
            return $res->withRedirect("/admin/edit-product/{$id}", 301);
        }
    }

    public function updateImg($req, $res, $args) {
        $id = $req->getParam('id');
        $actual = $req->getParam('actual');
        $new = $req->getUploadedFiles()['new'];

        $directory = self::DIR . $id;
        if ($actual) {
            $new->moveTo(self::DIR . $id . DIRECTORY_SEPARATOR . $actual);
            $_SESSION['msg'] =  array("type" => "success","msg" => "Imagen modificada");
            return $res->withRedirect("/admin/edit-product/{$id}", 301);
        }

        $pDao = new ProductDao();
        $filename = $this->addNewImgToFolder($directory, $id, $new);
        $result = $pDao->addImages($id, [$filename]);

        if (!$result) {
            $_SESSION['msg'] = array("type" => "error", "msg" => $pDao->getError());
            return $res->withRedirect("/admin/edit-product/{$id}", 301);
        } else {
            $_SESSION['msg'] = array("type" => "success", "msg" => 'Imagen añadida');
            return $res->withRedirect("/admin/edit-product/{$id}", 301);
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
        $id = $req->getParam('id');

        $productDao = new ProductDao();
        $result = $productDao->deleteById($id);

        if (!$result) {
            return $res->withHeader("Content-Type:", "text/html")
                            ->withStatus(400)
                            ->write($productDao->getError());
        } else {
            return $res->withHeader("Content-Type:", "text/html")
                            ->withStatus(200)
                            ->write('Producto Eliminado');
        }
    }

    function moveAndNameFile($directory, $productName, $imgs) {
        $filenames = array();
        $basename = md5($productName);
        foreach ($imgs as $key=>$img) {
            $extension = pathinfo($img->getClientFilename(), PATHINFO_EXTENSION);
            $filename = sprintf('%s.%0.8s', "$key-$basename" , $extension);
            $img->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
            array_push($filenames, $filename);
        }

        return $filenames;
    }

    function addNewImgToFolder($directory, $id, $img) {
        $pDao = new ProductDao();
        $actualImages = $pDao->getById($id)->getImg();
        $lastImage = explode("-", end($actualImages));
        $basename = $lastImage[0] + 1 . '-' . substr($lastImage[1], 0,32);
        $extension = pathinfo($img->getClientFilename(), PATHINFO_EXTENSION);
        $filename = sprintf('%s.%0.8s', $basename , $extension);
        $img->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }
}