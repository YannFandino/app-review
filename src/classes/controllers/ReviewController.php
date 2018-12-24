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

    public function listAll($req, $res, $args) {
        $reviewDao = new ReviewDao();
        $result = $reviewDao->getAll();

        if (empty($result)) {
            $msg = $reviewDao->getError() ? $reviewDao->getError() : "No hay valoraciones para mostrar";
            echo $msg;
        } else {
            echo "<ul>";
            foreach ($result as $arrayReview) {
                echo "<strong>{$arrayReview->getId()}</strong>";
                echo "<ul>";
                echo "<li>{$arrayReview->getProduct()}</li>";
                echo "<li>{$arrayReview->getUser()}</li>";
                echo "<li>{$arrayReview->getPoints()}</li>";
                echo "<li>{$arrayReview->getComment()}</li>";
                echo "<li>{$arrayReview->getDateCreated()}</li>";
                echo "<li>{$arrayReview->getLastModified()}</li>";
                echo "<li>{$arrayReview->getisApproved()}</li>";
                echo "</ul>";
            }
            echo "</ul>";
        }
    }

    public static function listById($id) {
        $reviewDao = new ReviewDao();
        $result = $reviewDao->listById($id);

        if (empty($result)) {
            $msg = $reviewDao->getError() ? $reviewDao->getError() : "No hay valoraciones para mostrar";
            return array("error" => $msg);
        } else {
            return $result;
        }
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
        $id = $args['id'];

        $reviewDao = new ReviewDao();
        $result = $reviewDao->deleteById($id);

        if (!$result) {
            echo $reviewDao->getError();
        } else {
            echo "Eliminada";
        }
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