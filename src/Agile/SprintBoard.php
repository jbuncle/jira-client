<?php

namespace JBuncle\JiraClient\Agile;

/**
 * Board.
 */
class SprintBoard {

    private array $json;

    public function __construct(array $json) {
        $this->json = $json;
    }

    public function getId() {
        return $this->json['id'];
    }

    public function getName() {
        return $this->json['name'];
    }

    public function getType() {
        return $this->json['type'];
    }

}
