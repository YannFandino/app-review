<?php
namespace Classes\models;


class Category {
    private $id;
    private $name;
    private $parent;

    /**
     * Category constructor.
     * @param $id
     * @param $name
     * @param $parent
     * @param $row
     */
    public function __construct($row, $id = null, $name = null, $parent = null) {
        $this->id = $row ? $row['id'] : $id;
        $this->name = $row ? $row['name'] : $name;
        $this->parent = $row ? $row['parent'] : $parent;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return ucwords($this->name);
    }

    public function getParent() {
        return $this->parent;
    }


}