<?php
namespace Classes\models;


class Review {
    private $id;
    private $product;
    private $user;
    private $multiplier;
    private $points;
    private $comment;
    private $date_created;
    private $last_modified;
    private $is_approved;
    private $username;

    /**
     * Review constructor.
     * @param $row
     * @param $id
     * @param $product
     * @param $user
     * @param $multiplier
     * @param $points
     * @param $comment
     * @param $date_created
     * @param $last_modified
     * @param $is_approved
     * @param $username
     */
    public function __construct($row, $id = null, $product = null, $user = null, $points = null,
                                $comment = null, $date_created = null, $last_modified = null,
                                $is_approved = null, $multiplier = null, $username = null) {
        $this->id = $row ? $row['id'] : $id;
        $this->product = $row ? $row['product_id'] : $product;
        $this->user = $row ? $row['user_id'] : $user;
        $this->multiplier = $row ? $row['multiplier'] : $multiplier;
        $this->points = $row ? $row['points'] : $points;
        $this->comment = $row ? $row['comment'] : $comment;
        $this->date_created = $row ? $row['date_created'] : $date_created;
        $this->last_modified = $row ? $row['last_modified'] : $last_modified;
        $this->is_approved = $row ? $row['is_approved'] : $is_approved;
        $this->username = isset($row['username']) ? $row['username'] : $username;
    }

    public function getId() {
        return $this->id;
    }

    public function getProduct() {
        return $this->product;
    }

    public function getUser() {
        return $this->user;
    }

    public function getPoints() {
        return $this->points;
    }

    public function getComment() {
        return $this->comment;
    }

    public function getDateCreated() {
        return $this->date_created;
    }

    public function getLastModified() {
        return $this->last_modified;
    }

    public function getisApproved() {
        return $this->is_approved;
    }

    public function getMultiplier() {
        return $this->multiplier;
    }

    public function getUsername() {
        return $this->username;
    }

}