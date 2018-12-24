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

    public function getById($id) {
        $userDao = new UserDao();
        $result = $userDao->findById($id);

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
        $isOk = false;
        $id = $req->getParam('id');
        $name = $req->getParam('name');
        $email = $req->getParam('email');
        $password = null;
        $oldPass = $req->getParam('old');
        $newPass = $req->getParam('new');
        $verify = $req->getParam('verify');
        $role_id = $req->getParam('role_id');

        if ($oldPass || $newPass || $verify) {
            if (!password_verify($oldPass, $_SESSION['user']->getPassword()))
                $_SESSION['error-pass'] = "La contraseña actual no coincide";
            else if (!$newPass)
                $_SESSION['error-pass'] = "Debe introducir una nueva contraseña";
            else if ($newPass != $verify)
                $_SESSION['error-pass'] = "No ha verificado la contraseña";
            else
                $isOk = true;
        }

        if ($isOk)
            $password = password_hash($newPass, PASSWORD_DEFAULT);
        else if (!$isOk && ($oldPass || $newPass || $verify))
            return $res->withRedirect('/profile', 301);

        $userDao = new UserDao();
        $result = $userDao->updateUser($id, $name, $email, $password, $role_id);

        if (!$result) {
            $_SESSION['error-modify'] = $userDao->getError();
        } else {
            $_SESSION['user'] = $this->getById($id);
            return $res->withRedirect('/profile', 301);
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