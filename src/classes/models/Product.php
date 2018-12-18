<?php
namespace Classes\models;


class Product {
    private $id;
    private $name;
    private $description;
    private $details;
    private $category;
    private $img;
    private $nReviews;
    private $points;

    /**
     * Product constructor.
     * @param $row
     * @param null $id
     * @param null $name
     * @param null $description
     * @param null $details
     * @param null $category
     * @param array|null $img
     * @param null $points
     */
    public function __construct($row, $id = null, $name = null, $description = null, $details = null, $category = null,
                                array $img = null, $points = null) {
        $this->id = $row ? $row['id'] : $id;
        $this->name = $row ? $row['name'] : $name;
        $this->description = $row ? $row['description'] : $description;
        $this->details = $row ? $row['details'] : $details;
        $this->category = $row ? $row['category_id'] : $category;
        $this->img = $row ? explode(",",$row['img']) : explode(',',$img);
        $this->nReviews = isset($row['reviews']) ? $row['reviews'] : null;
        $this->points = isset($row['media']) && $row['media'] ? $row['media'] : 0;
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

    public function getNReviews() {
        return $this->nReviews;
    }

    public function getPoints() {
        return $this->points;
    }


}