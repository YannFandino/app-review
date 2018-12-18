<?php
namespace Classes\controllers;
use Classes\controllers\UserController;
use Classes\models\User;

class HomeController {

    private $view;

    /**
     * HomeController constructor.
     * @param $view
     */
    public function __construct($c) {
        $this->view = $c['view'];
    }


    public function login($req, $res, $args) {
        $username = $req->getParam('username');
        $password = $req->getParam('password');

        $userController = new UserController();
        $user = $userController->getByUsername($username);

        if (!$user) {
            $_SESSION['error'] = "Usuario no existe";
            return $this->view->render($res, '/login.phtml', []);
        }
        if (!password_verify($password, $user->getPassword())) {
            $_SESSION['error'] = "Combinación usuario y contraseña no válida.";
            return $this->view->render($res, '/login.phtml', ["username" => $username]);
        }
        $_SESSION['user'] = $user;
        return $this->redirectAfterLogin($user, $res);
    }

    public function register($req, $res, $args) {
        $name = trim($req->getParam('name'));
        $username = trim($req->getParam('username'));
        $email = trim($req->getParam('email'));
        $password = $req->getParam('password');
        $verify = $req->getParam('verify');

        $args = array("name" => $name,
                      "username" => $username,
                      "email" => $email,
                      "password" => $password,
                      "verify" => $verify,
                      "role_id" => 3);

        $isError = $this->checkData($args);
        if (!empty($isError)) {
            $_SESSION['error'] = $isError;
            return $this->view->render($res, '/create-account.phtml', $args);
        }
        $userController = new UserController();
        $result = $userController->add($args);
        if (isset($result['error'])) {
            $_SESSION['error'] = $result;
            return $this->view->render($res, '/create-account.phtml', $args);
        }
        return $this->login($req, $res, $args);
    }

    public function logout($req, $res, $args) {
        $_SESSION = array();
        session_destroy();
        return $res->withRedirect('/', 301);
    }

    public function redirectAfterLogin(User $user, $res) {
        if ($user->getRol() == 1) {
            return $res->withRedirect('admin/panel', 301);
        } else {
            return $res->withRedirect('/', 301);
        }
    }

    private function checkData($args) {
        $error = [];
        if (!$args["name"]) {
            array_push($error, "Debe escribir su nombre");
        }
        if (!$args["username"]) {
            array_push($error, "Debe escribir un nombre de usuario");
        }
        if (!$args["email"]) {
            array_push($error, "Debe escribir su email");
        }
        if (!$args["password"]) {
            array_push($error, "Debe escribir una contraseña");
        }
        if ($args["password"] != $args["verify"]) {
            array_push($error, "La contraseña debe coincidir");
        }
        return $error;
    }
}