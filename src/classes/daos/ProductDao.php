<?php
namespace Classes\daos;

use Classes\models\Product;
use PDO;
use DBConnection;
use Exception;

class ProductDao {
    private $db;
    private $error;

    /**
     * ProductDao constructor.
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
    public function addProduct($name, $description, $details, $category) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            if (!$this->isProductExist($name)) {
                $sql = "INSERT INTO table_products (name, parent, details, category_id)
                        VALUES (:name, :description, :details, :category_id)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('name', $name);
                $stmt->bindParam('description', $description, PDO::PARAM_INT);
                $stmt->bindParam('details', $details, PDO::PARAM_STR);
                $stmt->bindParam('category_id', $category, PDO::PARAM_STR);
                $result = $stmt->execute();

                if ($result) {
                    $db->commit();
                    return true;
                }
            } else throw new Exception('El producto ya existe');
        } catch (Exception $e) {
            $db->rollBack();
            $this->setError($e->getMessage());
            return false;
        }

    }

    public function updateProduct($id, $name, $description, $details, $category) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $sql = "UPDATE table_products
                    SET name = :name,
                    description = :description,
                    details = :details,
                    category_id = :category_id
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('name', $name);
            $stmt->bindParam('description', $description, PDO::PARAM_INT);
            $stmt->bindParam('details', $details, PDO::PARAM_STR);
            $stmt->bindParam('category_id', $category, PDO::PARAM_STR);
            $result = $stmt->execute();

            if ($result) {
                $db->commit();
                return true;
            } else throw new Exception('Ha ocurrido un error al modificar el producto');

        } catch (Exception $e) {
            $db->rollBack();
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function getAll() {
        $db = $this->getDb();
        $list = array();
        $sql = "SELECT id, name, description, details, category_id
                FROM table_products 
                ORDER BY name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $product = new Product(null, $row['id'], $row['name'], $row['description'], $row['details'], $row['category_id']);
            $list[$product->getName()] = $product;
            };
        return $list;
    }

    public function deleteById($id) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $sql = "DELETE FROM table_products
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result && $stmt->rowCount()) {
                $db->commit();
                return true;
            } else throw new Exception('El producto no existe.');
        } catch (Exception $e) {
            $db->rollBack();
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function isProductExist($name) {
        $db = $this->getDb();
        $sql = "SELECT * FROM table_products c
                WHERE c.name = :name";
  
        $stmt = $db->prepare($sql);
        $stmt->bindParam('name', $name);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) return true;
        return false;
    }


}