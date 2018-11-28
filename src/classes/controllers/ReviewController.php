<?php
namespace Classes\controllers;
use Classes\daos\ReviewDao;

class ReviewController {

    public function add($args) {
        $product = strtolower($args['product']);
        $user = strtolower($args['user']);
        $points = isset($args['points']) ? $args['points'] : null;
        $comment = isset($args['comment']) ? $args['comment'] : null;
        $date_created = isset($args['date_created']) ? $args['date_created'] : null;
        $last_modified = isset($args['last_modified']) ? $args['last_modified'] : null;
        $is_approved = isset($args['is_approved']) ? $args['is_approved'] : null;

        $reviewDao = new ReviewDao();
        $result = $reviewDao->addReview($product, $user, $points, $comment, $date_created, $last_modified, $is_approved);

        if (!$result) {
            echo $reviewDao->getError();
        } else {
            echo "Insertado";
        }
    }

    public function update($req, $res, $args) {
        $id = $args['id'];
        $product = strtolower($args['product']);
        $user = strtolower($args['user']);
        $points = isset($args['points']) ? $args['points'] : null;
        $comment = isset($args['comment']) ? $args['comment'] : null;
        $date_created = isset($args['date_created']) ? $args['date_created'] : null;
        $last_modified = isset($args['last_modified']) ? $args['last_modified'] : null;
        $is_approved = isset($args['is_approved']) ? $args['is_approved'] : null;

        $reviewDao = new ReviewDao();
        $result = $reviewDao->updateReview($id, $product, $user, $points, $comment, $date_created, $last_modified, $is_approved);

        if (!$result) {
            echo $reviewDao->getError();
        } else {
            echo "Modificada";
        }
    }

    public function listAll() {
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