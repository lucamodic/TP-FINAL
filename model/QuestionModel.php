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

    public function addQuestionToAnswered($pregunta, $usuario){
        $sql = "INSERT INTO preguntas_usadas (username, pregunta_id) values ('$usuario', '$pregunta')";
        $this->database->execute($sql);
    }

    public function isAnswered($pregunta, $usuario){
        $sql = "SELECT * FROM preguntas_usadas 
         WHERE username LIKE '$usuario'
         AND pregunta_id LIKE '$pregunta'";
        return sizeof($this->database->query($sql)) > 0;
    }

    public function getQuestionsAsked($usuario){
        $sql = "SELECT * FROM preguntas_usadas 
         WHERE username LIKE '$usuario'";
        return sizeof($this->database->query($sql));
    }

    public function getQuestions(){
        $sql = "SELECT * FROM pregunta";
        return sizeof($this->database->query($sql));
    }

    public function deleteUserAnsweredQuestions($usuario){
        $sql = "DELETE FROM preguntas_usadas
        WHERE username = '$usuario'";
        $this->database->execute($sql);
    }

}