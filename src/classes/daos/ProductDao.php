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
            if (!$name) throw new Exception("Debe escribir el nombre del producto");
            if (!$description) throw new Exception("Debe añadir la descripción del producto");
            if (!$category) throw new Exception("Debe incluir la categoría del producto");
            if (!$this->isProductExist($name)) {
                $sql = "INSERT INTO table_products (name, description, details, category_id)
                        VALUES (:name, :description, :details, :category_id)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('name', $name);
                $stmt->bindParam('description', $description);
                $stmt->bindParam('details', $details);
                $stmt->bindParam('category_id', $category, PDO::PARAM_INT);
                $result = $stmt->execute();
                $id = $db->lastInsertId();
                if ($result) {
                    $db->commit();
                    return $id;
                }
            } else throw new Exception('El producto ya existe');
        } catch (Exception $e) {
            $db->rollBack();
            $this->setError($e->getMessage());
            return false;
        }

    }

    public function addImages($idProduct, $filenames) {
        $db = $this->getDb();
        $db->beginTransaction();
        foreach ($filenames as $filename) {
            try {
                $sql = "INSERT INTO table_images (url, product_id)
                        VALUES (:url, :product_id)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('url', $filename);
                $stmt->bindParam('product_id', $idProduct);
                $result = $stmt->execute();
            } catch (Exception $e) {
                $db->rollBack();
                $this->setError($e->getMessage());
                return false;
            }
        }
        $db->commit();
        return true;
    }

    public function updateImage($idProduct, $filename, $oldFilename) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $sql = "UPDATE table_images
                    SET url = :filename
                    WHERE product_id = :idProduct
                    AND url = :oldFilenale";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('filename', $filename);
            $stmt->bindParam('idProduct', $idProduct);
            $stmt->bindParam('oldFilenale', $oldFilename);
            $result = $stmt->execute();
            if ($result) $db->commit();
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
            if (!$name) throw new Exception("Debe escribir el nombre del producto");
            if (!$description) throw new Exception("Debe añadir la descripción del producto");
            if (!$category) throw new Exception("Debe incluir la categoría del producto");
            $sql = "UPDATE table_products
                    SET name = :name,
                    description = :description,
                    details = :details,
                    category_id = :category_id
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('name', $name);
            $stmt->bindParam('description', $description);
            $stmt->bindParam('details', $details);
            $stmt->bindParam('category_id', $category, PDO::PARAM_INT);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
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
        $sql = "SELECT p.*,
                       COUNT( DISTINCT r.id) as reviews,
                       GROUP_CONCAT( DISTINCT i.url) as img,
                       (SUM(r.points*r.multiplier)/SUM(r.multiplier)) as media
                FROM table_products p
                LEFT JOIN table_reviews r ON r.product_id = p.id
                LEFT JOIN table_images i ON i.product_id = p.id
                GROUP BY p.id
                ORDER BY p.id ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $product = new Product($row);
            array_push($list, $product);
        };
        return $list;
    }

    public function getPopular() {
        $db = $this->getDb();
        $list = array();
        $sql = "SELECT p.*,
                       COUNT( DISTINCT r.id) as reviews,
                       GROUP_CONCAT( DISTINCT i.url) as img,
                       (SUM(r.points*r.multiplier)/SUM(r.multiplier)) as media
                FROM table_products p
                LEFT JOIN table_reviews r ON r.product_id = p.id
                LEFT JOIN table_images i ON i.product_id = p.id
                WHERE r.points IS NOT NULL 
                GROUP BY p.id
                ORDER BY reviews DESC
                LIMIT 4";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $product = new Product($row);
            array_push($list, $product);
        };
        return $list;
    }

    public function getNewOnes() {
        $db = $this->getDb();
        $list = array();
        $sql = "SELECT p.*,
                       COUNT(DISTINCT r.id) as reviews,
                       GROUP_CONCAT(DISTINCT i.url) as img,
                       (SUM(r.points*r.multiplier)/SUM(r.multiplier)) as media
                FROM table_products p
                LEFT JOIN table_reviews r ON r.product_id = p.id
                LEFT JOIN table_images i ON i.product_id = p.id
                GROUP BY p.id
                ORDER BY p.id DESC
                LIMIT 4";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $product = new Product($row);
            array_push($list, $product);
        };
        return $list;
    }

    public function getByCategory($id) {
        $db = $this->getDb();

        $ids = self::getChildrenCategories($id);
        array_push($ids, $id);

        $in = implode(',',$ids);

        $list = array();
        $sql = "SELECT p.*,
                       COUNT(DISTINCT r.id) as reviews,
                       GROUP_CONCAT(DISTINCT i.url) as img,
                       (SUM(r.points*r.multiplier)/SUM(r.multiplier)) as media
                FROM table_products p
                LEFT JOIN table_reviews r ON r.product_id = p.id
                LEFT JOIN table_images i ON i.product_id = p.id
                WHERE p.category_id IN ($in)
                GROUP BY p.id
                ORDER BY p.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $product = new Product($row);
            array_push($list, $product);
        };
        return $list;
    }

    private function getChildrenCategories($id) {
        $db = $this->getDb();
        $sql = "SELECT id FROM table_categories
                WHERE parent = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $list = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return $list;
    }

    public function getById($id) {
        $db = $this->getDb();
        $sql = "SELECT p.*,
                       COUNT(DISTINCT r.id) as reviews,
                       GROUP_CONCAT(DISTINCT i.url) as img,
                       (SUM(r.points*r.multiplier)/SUM(r.multiplier)) as media
                FROM table_products p
                LEFT JOIN table_reviews r ON r.product_id = p.id
                LEFT JOIN table_images i ON i.product_id = p.id
                WHERE p.id = :id
                GROUP BY p.id
                ORDER BY p.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) return new Product($row);
        return false;
    }

    public function deleteById($id) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $sql = "DELETE FROM table_images
                    WHERE product_id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if (!$result) throw new Exception("Ha ocurrido un error. Comuníquese con el Administrador.");

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
            switch ($e->getCode()) {
                case '23000':
                    $this->setError('Se deben eliminar las valoraciones antes de borrar el producto');
                    break;
                default:
                    $this->setError($e->getMessage());
            }
            return false;
        }
    }

    public function search($args) {
        $db = $this->getDb();
        try {
            $sql = "SELECT p.*,
                       COUNT(DISTINCT r.id) as reviews,
                       GROUP_CONCAT(DISTINCT i.url) as img,
                       (SUM(r.points*r.multiplier)/SUM(r.multiplier)) as media
                    FROM table_products p
                    LEFT JOIN table_reviews r ON r.product_id = p.id
                    LEFT JOIN table_images i ON i.product_id = p.id
                    WHERE";
            foreach ($args as $key => $arg) {
                $upper = strtoupper($arg);
                $or = $key == 0 ? "" : " OR";
                $sql .= "$or UPPER(p.name) LIKE '%$upper%'
                    OR UPPER(p.description) LIKE '%$upper%'
                    OR UPPER(p.details) LIKE '%$upper%'";
            }
            $sql .= "GROUP BY p.id
                     ORDER BY p.id DESC";

            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $list = array();
            while ($row = $stmt->fetch()) {
                $product = new Product($row);
                array_push($list, $product);
            };
            return $list;

        } catch (Exception $e) {

        };

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