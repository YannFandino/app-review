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

    /** Añadir nuevo prodcuto
     * @param $name
     * @param $description
     * @param $details
     * @param $category
     * @return boolean
     */
    public function addReview($product, $user, $points, $comment, $date_created, $last_modified, $is_approved) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            // if (!$this->isReviewExist($product, $user)) {
                $sql = "INSERT INTO table_reviews (product_id, user_id, points, comment, date_created, last_modified, is_approved)
                        VALUES (:product_id, :user_id, :points, :comment, :date_created, :last_modified, :is_approved)";

                $stmt = $db->prepare($sql);
                // $stmt->bindParam('product_id', $product);
                // $stmt->bindParam('user_id', $user, PDO::PARAM_INT);
                // $stmt->bindParam('date_created', $date_created, PDO::PARAM_STR);
                // $stmt->bindParam('last_modified', $last_modified, PDO::PARAM_STR);
                // $stmt->bindParam('is_approved', $is_approved, PDO::PARAM_BOOL);
                $stmt->bindParam('product_id', $a = 1);
                $stmt->bindParam('user_id', $b =1);
                $stmt->bindParam('points', $points, PDO::PARAM_STR);
                $stmt->bindParam('comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam('date_created', $e='asadasa');
                $stmt->bindParam('last_modified',$g='asasadas');
                $stmt->bindParam('is_approved', $h=false);
                $result = $stmt->execute();

                if ($result) {
                    $db->commit();
                    return true;
                }
            // } else throw new Exception('Ya has valorado este producto');
        } catch (Exception $e) {
            $db->rollBack();
            $this->setError($e->getMessage());
            return false;
        }

    }

    public function updateReview($id, $product, $user, $points, $comment, $date_created, $last_modified, $is_approved) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $sql = "UPDATE table_reviews
                    SET product_id = :product_id,
                    user_id = :user_id,
                    comment = :comment,
                    date_created = :date_created,
                    last_modified = :last_modified,
                    is_approved = :is_approved
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('product_id', $product);
            $stmt->bindParam('user_id', $user, PDO::PARAM_INT);
            $stmt->bindParam('comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam('date_created', $date_created, PDO::PARAM_STR);
            $stmt->bindParam('last_modified', $last_modified, PDO::PARAM_STR);
            $stmt->bindParam('is_approved', $is_approved, PDO::PARAM_BOOL);
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
        $sql = "SELECT *
                FROM table_reviews 
                ORDER BY product_id ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $review = new Review(null, $row['id'], $row['product_id'], $row['user_id'], $row['points'], $row['comment'], $row['date_created'], $row['last_modified'], $row['is_approved']);
            $list[$review->getId()] = $review;
        };
        return $list;
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
        $sql = "SELECT * FROM table_reviews c
                WHERE c.name = :name
                AND c.user = :user";
  
        $stmt = $db->prepare($sql);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('user', $user);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) return true;
        return false;
    }


}