<?php
namespace Classes\models;


class User {
    private $id;
    private $name;
    private $username;
    private $email;
    private $password;
    private $date_registered;
    private $rol;

    /**
     * User constructor.
     * @param $row
     * @param $id
     * @param $name
     * @param $username
     * @param $email
     * @param $password
     * @param $date_registered
     * @param $rol
     */
    public function __construct($row, $id = null, $name = null, $username = null, $email = null,
                                $password = null, $date_registered = null, $rol = null) {
        $this->id = $row ? $row['id'] : $id;
        $this->name = $row ? $row['name'] : $name;
        $this->username = $row ? $row['username'] : $username;
        $this->email = $row ? $row['email'] : $email;
        $this->password = $row ? $row['password'] : $password;
        $this->date_registered = $row ? $row['date_registered'] : $date_registered;
        $this->rol = $row ? $row['rol'] : $rol;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return ucwords($this->name);
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getDateRegistered() {
        return $this->date_registered;
    }

    public function getRol() {
        return $this->rol;
    }
}