<?php
namespace Classes\controllers;
use Classes\daos\ReviewDao;

class ReviewController {

    public function add($req, $res, $args) {
        $product = $req->getParam('product_id');
        $user = $req->getParam('user_id');
        $points = $req->getParam('points');
        $comment = mb_strtolower($req->getParam('comment'));

        $reviewDao = new ReviewDao();
        $result = $reviewDao->addReview($product, $user, $points, $comment);

        if (!$result) {
            $msg = $reviewDao->getError() ? $reviewDao->getError() : "Ha ocurrido un error al hacer una valoración";
            echo $msg;
//            return array("error" => $msg);
        } else {
            echo "Valoración añadida";
//            return true;
        }
        echo "<br><a href='/'>Inicio</a>";
    }

    public function update($req, $res, $args) {
        $id = $req->getParam('id');
        $points = $req->getParam('points');
        $comment = mb_strtolower($req->getParam('comment'));

        $reviewDao = new ReviewDao();
        $result = $reviewDao->updateReview($id, $points, $comment);

        if (!$result) {
            echo $reviewDao->getError();
        } else {
            echo "Modificada";
        }
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

}