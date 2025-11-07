<?php

class Signalement {
    private $id;
    private $raison;

    public function __construct($id, $raison) {
        $this->id = $id;
        $this->raison = $raison;
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function getRaison() {
        return $this->raison    ;
    }

    public function setRaison($raison) {
        $this->raison = $raison;
    }
}
