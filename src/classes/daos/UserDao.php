<?php
namespace Classes\daos;

use Classes\models\User;
use PDO;
use DBConnection;
use Exception;


class UserDao {
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

    public function addUser($name, $username, $email, $password, $rol_id) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            if ($this->isEmailExist($email)) throw new Exception('El email ya existe.');
            if ($this->isUsernameExist($username)) throw new Exception('El usuario ya existe.');

            $sql = "INSERT INTO table_users (name, username, email, password, date_registered, rol_id)
                    VALUES (:name, :username, :email, :password, CURRENT_DATE, :rol_id)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('name', $name);
            $stmt->bindParam('username', $username);
            $stmt->bindParam('email', $email);
            $stmt->bindParam('password', $password);
            $stmt->bindParam('rol_id', $rol_id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                $db->commit();
                return true;
            }
            return false;
        } catch (Exception $e) {
            $db->rollBack();
            switch ($e->getCode()) {
                case '23000':
                    $this->setError('Debe seleccionar un rol válido.');
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
        $sql = "SELECT * FROM table_users
                WHERE rol_id NOT IN (1)";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $user = new User($row);
            array_push($list, $user);
        };
        return $list;
    }

    public function getByRole($role) {
        $db = $this->getDb();
        $sql = "SELECT * FROM table_users u
                WHERE u.rol_id = :role";

        $stmt = $db->prepare($sql);
        $stmt->bindParam('role', $role);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) return new User($row);
        return false;
    }

    public function updateUser($id, $name = null, $email = null, $password = null, $role_id = null) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $sql = "UPDATE table_users SET ";
            if ($name) $sql .= " name = :name,";
            if ($email) $sql .= " email = :email,";
            if ($password) $sql .= " password = :password,";
            if ($role_id) $sql .= " rol_id = :role_id,";
            $sql = substr($sql, 0, strlen($sql)-1);
            $sql .= " WHERE id = :id";

            $stmt = $db->prepare($sql);
            if ($name) $stmt->bindParam('name', $name);
            if ($email) $stmt->bindParam('email', $email);
            if ($password) $stmt->bindParam('password', $password);
            if ($role_id) $stmt->bindParam('rol_id', $role_id);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                $db->commit();
                return true;
            } else throw new Exception('Ha ocurrido un error al modificar el usuario');
        } catch (Exception $e) {
            $db->rollBack();
            switch ($e->getCode()) {
                case '23000':
                    $this->setError('Debe seleccionar un rol válido.');
                    break;
                default:
                    $this->setError($e->getMessage());
            }
            return false;
        }
    }

    public function deleteById($id) {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $sql = "DELETE FROM table_users
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result && $stmt->rowCount()) {
                $db->commit();
                return true;
            } else throw new Exception('El usuario no existe.');
        } catch (Exception $e) {
            $db->rollBack();
            switch ($e->getCode()) {
                case '23000':
                    $this->setError('No se ha podido eliminar.');
                    break;
                default:
                    $this->setError($e->getMessage());
            }
            return false;
        }
    }

    /**
     * @param $email
     * @return bool|User
     */
    public function isEmailExist($email) {
        $db = $this->getDb();
        $sql = "SELECT * FROM table_users u
                WHERE u.email = :email";

        $stmt = $db->prepare($sql);
        $stmt->bindParam('email', $email);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) return new User($row);
        return false;
    }

    /**
     * @param $username
     * @return bool|User
     */
    public function isUsernameExist($username) {
        $db = $this->getDb();
        $sql = "SELECT * FROM table_users u
                WHERE u.username = :username";

        $stmt = $db->prepare($sql);
        $stmt->bindParam('username', $username);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) return new User($row);
        return false;
    }

    public function findById($id) {
        $db = $this->getDb();
        $sql = "SELECT * FROM table_users u
                WHERE u.id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) return new User($row);
        return false;
    }
}