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

    public function agarrarUltimaPregunta($usuario){
        $result = $this->buscarPreguntaActual($usuario);
        return $this->getPreguntaById($result[0]['pregunta_id']);
    }

    public function buscarPreguntaActual($usuario){
        $sql = "SELECT * FROM preguntas_usadas 
         WHERE username LIKE '$usuario'
         ORDER BY tiempo DESC
        LIMIT 1";
        return $this->database->query($sql);
    }

    public function getPreguntaById($id){
        $sql = "SELECT * FROM pregunta 
         WHERE id = '$id'";
        return $this->database->query($sql);
    }
    public function agregarPreguntaReportada($idPreguntaReportada){
        $sql = "INSERT INTO preguntas_reportadas  (pregunta_id) values ($idPreguntaReportada)";
        $this->database->execute($sql);
    }
    public function getPreguntasReportadas(){
        $sql = "SELECT * FROM preguntas_reportadas";
        $preguntas = $this->database->query($sql);
        $preguntasReportadas = array();
        foreach ($preguntas as $pregunta){
             $preguntaBuscada = $pregunta['pregunta_id'];
             $sql = "SELECT * FROM pregunta Where id LIKE '$preguntaBuscada'";
             $preguntasReportadas[] = $this->database->query($sql)[0];
        }
        return $preguntasReportadas;
    }
    public function getCategorias(){
        $sql="SELECT DISTINCT categorias
                FROM preguntas;";
        return $this->database->query($sql);
    }
}