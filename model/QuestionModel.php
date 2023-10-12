<?php

class QuestionModel{

    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function getRandomQuestion() {
        $sql = "SELECT * FROM pregunta";
        $resultado = $this->database->query($sql);
        $random = rand(0, sizeof($resultado)-1);
        return $resultado[$random];
    }

}