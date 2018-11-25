<?php
namespace Classes\daos;

use Classes\models\Category;
use PDO;
use DBConnection;
use Exception;

class CategoryDao {
    private $db;
    private $error;

    /**
     * CategoryDao constructor.
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

    /** Añadir nueva categoría
     * @param $name
     * @param null $parent
     * @return boolean
     */
    public function addCategory($name, $parent) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            if (!$this->isCategoryExist($name, $parent)) {
                $sql = "INSERT INTO table_categories (name, parent)
                        VALUES (:name, :parent)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('name', $name);
                $stmt->bindParam('parent', $parent, PDO::PARAM_INT);
                $result = $stmt->execute();

                if ($result) {
                    $db->commit();
                    return true;
                }
                return false;
            } else throw new Exception('La categoría ya existe');
        } catch (Exception $e) {
            $db->rollBack();
            switch ($e->getCode()) {
                case '23000':
                    $this->setError('Debe seleccionar una categoría padre válida.');
                    break;
                default:
                    $this->setError($e->getMessage());
            }
            return false;
        }

    }

    public function updateCategory($id, $name, $parent) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $sql = "UPDATE table_categories
                    SET name = :name,
                        parent = :parent
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('name', $name);
            $stmt->bindParam('parent', $parent, PDO::PARAM_INT);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                $db->commit();
                return true;
            } else throw new Exception('Ha ocurrido un error al modificar la categoría');

        } catch (Exception $e) {
            $db->rollBack();
            switch ($e->getCode()) {
                case '23000':
                    $this->setError('Debe seleccionar una categoría padre válida.');
                    break;
                default:
                    $this->setError($e->getMessage());
            }
            return false;
        }
    }

    public function getAll() {
        $db = $this->getDb();
        $list = array();
        $sql = "SELECT parent.id as parent_id, 
                       parent.name as parent_name,
                       child.*
                FROM table_categories child LEFT JOIN table_categories parent
                ON child.parent = parent.id
                ORDER BY parent_name ASC, child.parent, child.name;";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $id_parent = $row['parent_id'];
            $name_parent = ucwords($row['parent_name']);
            $category = new Category(null, $row['id'],$row['name'],$row['parent']);
            if ($id_parent) {
                //es subcategoria
                array_push($list[$name_parent]['childs'],$category);
            } else {
                $array = array("parentInfo" => $category,
                    "childs" => array());
                $list[$category->getName()] = $array;
            }
        };
        return $list;
    }

    public function getAllParents() {
        $db = $this->getDb();
        $list = array();
        $sql = "SELECT *
                FROM table_categories
                WHERE parent IS NULL
                ORDER BY name ASC;";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $category = new Category($row);
            array_push($list, $category);
        };
        return $list;
    }

    public function deleteById($id) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $sql = "DELETE FROM table_categories
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result && $stmt->rowCount()) {
                $db->commit();
                return true;
            } else throw new Exception('La categoría no existe.');
        } catch (Exception $e) {
            $db->rollBack();
            switch ($e->getCode()) {
                case '23000':
                    $this->setError('No se ha podido eliminar, la categoría tiene subcategorías hijas o existen podructos en esta categoría.');
                    break;
                default:
                    $this->setError($e->getMessage());
            }
            return false;
        }
    }

    public function isCategoryExist($name, $parent) {
        $db = $this->getDb();
        $sql = "SELECT * FROM table_categories c
                WHERE c.name = :name
                AND c.parent ";
        if (!$parent)
            $sql .= "IS NULL";
        else
            $sql .= "= :parent";

        $stmt = $db->prepare($sql);
        $stmt->bindParam('name', $name);
        if ($parent)
            $stmt->bindParam('parent', $parent, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) return true;
        return false;
    }
}