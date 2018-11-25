<?php
namespace Classes\controllers;
use Classes\daos\UserDao;

class UserController {
    public function add($args) {
        $name = mb_strtolower($args['name']);
        $username = strtolower($args['username']);
        $email = strtolower($args['email']);
        $password = password_hash($args['password'], PASSWORD_DEFAULT);
        $rol_id = $args['role_id'];

        $userDao = new UserDao();
        $result = $userDao->addUser($name, $username, $email, $password, $rol_id);

        if (!$result) {
            $msg = $userDao->getError() ? $userDao->getError() : "Ha ocurrido un error al intentar crear un usuario";
            return array("error" => $msg);
        } else {
            return true;
        }
    }

    public function listAll($req, $res, $args) {
        $userDao = new UserDao();
        $result = $userDao->getAll();

        if (empty($result)) {
            $msg = $userDao->getError() ? $userDao->getError() : "No hay usuarios para mostrar";
            echo $msg;
        } else {
            var_dump($result);
        }
    }

    public function getByEmail($req, $res, $args) {
        $email = strtolower($args['email']);
        $userDao = new UserDao();
        $result = $userDao->isEmailExist($email);
        if ($result) {
            var_dump($result);
        } else {
            echo "No existe usuario";
        }
    }

    public function getByUsername($username) {
        $userDao = new UserDao();
        $result = $userDao->isUsernameExist($username);
        if ($result) {
            return $result;
        }
        return false;
    }

    public function getByRole($req, $res, $args) {
        $rol = $args['role_id'];
        $userDao = new UserDao();
        $result = $userDao->getByRole($rol);
        if ($result) {
            var_dump($result);
        } else {
            echo "No existe usuario";
        }
    }

    public function update($req, $res, $args) {
        $id = $args['id'];
        $name = $args['name'];
        $email = $args['email'];
        $password = password_hash($args['password'], PASSWORD_DEFAULT);
        $role_id = $args['role_id'];

        $userDao = new UserDao();
        $result = $userDao->updateUser($id, $name, $email, $password, $role_id);

        if (!$result) {
            echo $userDao->getError();
        } else {
            echo "Modificado";
        }
    }

    public function delete($req, $res, $args) {
        $id = $args['id'];

        $userDao = new UserDao();
        $result = $userDao->deleteById($id);

        if (!$result) {
            echo $userDao->getError();
        } else {
            echo "Usuario Eliminado";
        }
    }
}