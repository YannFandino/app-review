<?php
namespace Classes\models;


class Product {
    private $id;
    private $name;
    private $description;
    private $details;
    private $category;
    private $img;

    /**
     * Product constructor.
     * @param $row
     * @param $id
     * @param $name
     * @param $description
     * @param $details
     * @param $category
     * @param $img
     */
    public function __construct($row, $id = null, $name = null, $description = null, $details = null, $category = null, array $img = null) {
        $this->id = $row ? $row['id'] : $id;
        $this->name = $row ? $row['name'] : $name;
        $this->description = $row ? $row['description'] : $description;
        $this->details = $row ? $row['details'] : $details;
        $this->category = $row ? $row['category_id'] : $category;
        $this->img = $row ? explode(",",$row['img']) : explode(',',$img);
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return ucfirst($this->name);
    }

    public function getDescription() {
        return ucfirst($this->description);
    }

    public function getDetails() {
        return $this->details;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getImg() {
        return $this->img;
    }
}