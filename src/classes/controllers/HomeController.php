<?php
namespace Classes\controllers;
use Classes\controllers\UserController;
use Classes\daos\ProductDao;
use Classes\daos\ReviewDao;
use Classes\daos\UserDao;
use Classes\models\User;

class HomeController {

    const LEVEL_1_SENIORITY = 180;
    const LEVEL_2_SENIORITY = 730;
    const LEVEL_1_POSTS = 25;
    const LEVEL_2_POSTS = 50;
    const ROLE_ADMIN = 1;
    const ROLE_MOD = 2;

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

        /**
         * Verificar rol de usuario
         * Si este cumple las condiciones, subira de nivel.
         */
        if ($user) {
            self::checkRole($user);
        }

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

    private function checkRole(User $user) {
        $userRol = $user->getRol();
        if ($userRol == self::ROLE_ADMIN || $userRol == self::ROLE_MOD)
            return;

        $id = $user->getId();
        $dateRegistered = strtotime($user->getDateRegistered());
        $seniority = round((time() - $dateRegistered)/(60 * 60 * 24));

        $rDao = new ReviewDao();
        $reviews = $rDao->getCountByUser($id)['count'];

        if (($seniority > self::LEVEL_2_SENIORITY) && $reviews > self::LEVEL_2_POSTS) {
            // Antiguedad mas de dos años y mas de 50 valoraciones
            $rol = 5;
        } elseif (($seniority > self::LEVEL_1_SENIORITY && $seniority <= self::LEVEL_2_SENIORITY) &&
                  ($reviews > self::LEVEL_1_POSTS && $reviews <= self::LEVEL_2_POSTS)) {
            // Antiguedad entre 6 meses y 2 años, y entre 25 y 50 valoraciones
            $rol = 4;
        } elseif ($seniority <= self::LEVEL_1_SENIORITY && $reviews <= self::LEVEL_1_POSTS) {
            // Antiguedad menor a 6 meses y menos de 25 valoraciones
            $rol = 3;
        } else {
            $rol = $userRol;
        }

        if ($userRol < $rol) {
            $uDao = new UserDao();
            $uDao->updateUser($id, null, null, null, $rol);
        }
    }

    public function search($req, $res, $args) {
        $args = explode(' ', $req->getParam('args'));
        $pDao = new ProductDao();
        $products = $pDao->search($args);

        if (!$products) {
            $products['error'] = "No se encontraron productos con esos criterios";
        }
        return $this->view->render($res, 'list-review.phtml', ['products' => $products]);;
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