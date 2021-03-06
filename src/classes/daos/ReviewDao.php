<?php
namespace Classes\daos;

use Classes\models\Review;
use PDO;
use DBConnection;
use Exception;

class ReviewDao {
    private $db;
    private $error;

    /**
     * ReviewDao constructor.
     * @param $conn
     */
    public function __construct() {
        $conn = new DBConnection();
        $this->db = $conn->getConn();
    }

    public function getDb() {
        return $this->db;
    }

    public function setError($error) {
        $this->error = $error;
    }

    public function getError() {
        return $this->error;
    }

    /**
     * @param $product
     * @param $user_id
     * @param $points
     * @param $comment
     * @param $multiplier
     * @return bool
     */
    public function addReview($product, $user_id, $multiplier, $points, $comment) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            if (!$product) throw new Exception("Debe seleccionar un producto");
            if (!$points) throw new Exception("Debe puntuar el producto");
            if (!$comment) throw new Exception("Debe incluir incluir un comentario");
            if (!$this->isReviewExist($product, $user_id)) {
                $sql = "INSERT INTO table_reviews (product_id, user_id, multiplier, points, comment, date_created)
                        VALUES (:product_id, :user_id, :multiplier, :points, :comment, CURRENT_DATE)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('product_id', $product, PDO::PARAM_INT);
                $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam('multiplier', $multiplier, PDO::PARAM_INT);
                $stmt->bindParam('points', $points);
                $stmt->bindParam('comment', $comment);
                $result = $stmt->execute();

                if ($result) {
                    $db->commit();
                    return true;
                }
            } else throw new Exception('Ya has valorado este producto');
        } catch (Exception $e) {
            $db->rollBack();
            $this->setError($e->getMessage());
            return false;
        }

    }

    public function updateReview($id, $points, $comment) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            if (!$points) throw new Exception("Debe dar una puntuación al producto");
            if (!trim($comment)) throw new Exception("Debe escribir la valoración");
            $sql = "UPDATE table_reviews
                    SET points = :points,
                        comment = :comment,
                        last_modified = CURRENT_DATE
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('points', $points, PDO::PARAM_INT);
            $stmt->bindParam('comment', $comment);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                $db->commit();
                return true;
            } else throw new Exception('Ha ocurrido un error al modificar la review');

        } catch (Exception $e) {
            $db->rollBack();
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function getAll() {
        $db = $this->getDb();
        $list = array();
        $sql = "SELECT r.*, u.username
                FROM table_reviews r
                INNER JOIN table_users u ON r.user_id = u.id
                ORDER BY r.product_id,r.date_created ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $review = new Review($row);
            $list[$review->getId()] = $review;
            };
        return $list;
    }

    public function getAllPending() {
        $db = $this->getDb();
        $list = array();
        $sql = "SELECT r.*, u.username
                FROM table_reviews r
                INNER JOIN table_users u ON r.user_id = u.id
                WHERE r.is_approved IS FALSE
                ORDER BY r.date_created ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $review = new Review($row);
            $list[$review->getId()] = $review;
        };
        return $list;
    }

    public function listById($id) {
        $db = $this->getDb();
        $list = array();
        $sql = "SELECT r.*, u.username
                FROM table_reviews r
                INNER JOIN table_users u ON r.user_id = u.id
                WHERE product_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $review = new Review($row);
            array_push($list, $review);
        };
        return $list;
    }

    public function getById($id) {
        $db = $this->getDb();
        $list = array();
        $sql = "SELECT r.*, u.username
                FROM table_reviews r
                INNER JOIN table_users u ON r.user_id = u.id
                WHERE r.id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        if ($row) return new Review($row);
        return false;
    }

    public function getCountByUser($id) {
        $db = $this->getDb();
        $list = array();
        $sql = "SELECT COUNT(1) as 'count'
                FROM table_reviews r
                WHERE r.user_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result;
    }

    public function approve($id) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            if (!$id) throw new Exception("Ha ocurrido un error.");
            $sql = "UPDATE table_reviews
                    SET is_approved = TRUE
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                $db->commit();
                return true;
            } else throw new Exception('Ha ocurrido un error al aprobar la valoración');

        } catch (Exception $e) {
            $db->rollBack();
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function deleteById($id) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $sql = "DELETE FROM table_reviews
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result && $stmt->rowCount()) {
                $db->commit();
                return true;
            } else throw new Exception('La review no existe.');
        } catch (Exception $e) {
            $db->rollBack();
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function isReviewExist($product, $user) {
        $db = $this->getDb();
        $sql = "SELECT * FROM table_reviews r
                WHERE r.product_id = :product
                AND r.user_id = :user";
  
        $stmt = $db->prepare($sql);
        $stmt->bindParam('product', $product);
        $stmt->bindParam('user', $user);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) return new Review($row);
        return false;
    }


}