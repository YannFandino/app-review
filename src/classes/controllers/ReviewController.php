<?php
namespace Classes\controllers;
use Classes\daos\ReviewDao;
use Classes\models\User;

class ReviewController {

    public function add($req, $res, $args) {
        $product = $req->getParam('product_id');
        $user = $_SESSION['user'];
        if (!$user) {
            $_SESSION['msg'] = array("type" => "error","msg" => "Debe estar registrado para valorar un producto");
            return $res->withRedirect("/add-review/$product", 301);
        }
        $userId = $user->getId();
        $multiplier = self::selectMultiplier($user->getRol());
        $points = $req->getParam('points');
        if (!$points) {
            $_SESSION['msg'] = array("type" => "error","msg" => "Debe dar una puntuación al producto");
            return $res->withRedirect("/add-review/$product", 301);
        }
        $comment = mb_strtolower($req->getParam('comment'));

        $reviewDao = new ReviewDao();
        $result = $reviewDao->addReview($product, $userId, $multiplier, $points, $comment);

        if (!$result) {
            $msg = $reviewDao->getError() ? $reviewDao->getError() : "Ha ocurrido un error al hacer una valoración";
            $_SESSION['msg'] = array("type" => "error","msg" => $msg);
            return $res->withRedirect("/add-review/$product", 301);
        }
        return $res->withRedirect("/product/$product", 301);
    }

    public function update($req, $res, $args) {
        $id = $req->getParam('id');
        $product_id = $req->getParam('product_id');
        $points = $req->getParam('points');
        $comment = mb_strtolower($req->getParam('comment'));

        $reviewDao = new ReviewDao();
        $result = $reviewDao->updateReview($id, $points, $comment);

        if (!$result) {
            $_SESSION['msg'] = array("type" => "error", "msg" => $reviewDao->getError());
            return $res->withRedirect("/edit-review/$product_id", 301);
        }
        $_SESSION['msg'] = array("type" => "success", "msg" => "Valoración modificada");
        return $res->withRedirect("/edit-review/$product_id", 301);
    }

    public static function listAll() {
        $reviewDao = new ReviewDao();
        $result = $reviewDao->getAll();

        if (empty($result)) {
            $msg = $reviewDao->getError() ? $reviewDao->getError() : "No hay valoraciones para mostrar";
            return array("error" => $msg);
        }
        return $result;
    }

    public static function listPending() {
        $reviewDao = new ReviewDao();
        $result = $reviewDao->getAllPending();

        if (empty($result)) {
            $msg = $reviewDao->getError() ? $reviewDao->getError() : "No hay valoraciones para mostrar";
            return array("error" => $msg);
        }
        return $result;
    }

    public static function listByProductId($id) {
        $reviewDao = new ReviewDao();
        $result = $reviewDao->listById($id);

        if (empty($result)) {
            $msg = $reviewDao->getError() ? $reviewDao->getError() : "No hay valoraciones para mostrar";
            return array("error" => $msg);
        }
        return $result;
    }

    public static function getById($id) {
        $reviewDao = new ReviewDao();
        $result = $reviewDao->getById($id);

        if (!$result) {
            $msg = $reviewDao->getError() ? $reviewDao->getError() : "No hay valoraciones para mostrar";
            $_SESSION['error'] = $msg;
            return false;
        }
        return $result;
    }

    public static function approve($id) {
        $reviewDao = new ReviewDao();
        $result = $reviewDao->approve($id);

        if (!$result) {
            return false;
        }
        return true;
    }

    public static function getByProductAndUser($product, $user) {
        $reviewDao = new ReviewDao();
        $result = $reviewDao->isReviewExist($product, $user);

        if ($result) {
            return $result;
        }
        return false;
    }

    public function delete($req, $res, $args) {
        $id = $req->getParam('id');

        $reviewDao = new ReviewDao();
        $result = $reviewDao->deleteById($id);

        if (!$result) {
            $_SESSION['error'] = "Ha ocurido un error";
            return $res->withRedirect("/admin/review/$id", 301);
        }
        return $res->withRedirect('/admin/reviews/', 301);
    }

    private function selectMultiplier($rol) {
        $multiplier = 0;
        switch ($rol) {
            case 3:
                $multiplier = 1;
                break;
            case 4:
                $multiplier = 2;
                break;
            case 5:
                $multiplier = 3;
        }
        return $multiplier;
    }
}