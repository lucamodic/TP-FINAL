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
            $sql = "UPDATE pregunta SET reportada = 1 WHERE id = '$idPreguntaReportada'";
            $this->database->execute($sql);
    }

    public function getPreguntasReportadas(){
        $sql = "SELECT * FROM pregunta WHERE reportada = 1";
        return $this->database->query($sql);
    }
    public function getCategorias(){
        $sql="SELECT DISTINCT categoria
                FROM pregunta;";
        return $this->database->query($sql);
    }

    public function setPreguntasAgregadas($data){
        $enunciado=$data["enunciado"];
        $categoria=$data["categoria"];
        $respuesta1=$data["respuesta1"];
        $respuesta2=$data["respuesta2"];
        $respuesta3=$data["respuesta3"];
        $respuesta4=$data["respuesta4"];
        //CREAR TABLA Y RESOLVER CATEGORIA NUEVA
        $sql = "INSERT INTO pregunta(categoria, enunciado, dificultad, reportada, agregada)
        values('$categoria', '$enunciado', 'facil', false, false);";
        $this->database->execute($sql);

        $last_inserted_id = $this->database->getId();

        $sql = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta1', '$last_inserted_id', false);";
        $this->database->execute($sql);
        $sql = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta2', '$last_inserted_id', false);";
        $this->database->execute($sql);
        $sql = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta3', '$last_inserted_id', false);";
        $this->database->execute($sql);
        $sql = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta4', '$last_inserted_id', true);";
        $this->database->execute($sql);

    }
    public function getPreguntasNuevas(){
        $sql = "SELECT * FROM pregunta WHERE agregada = 0";
        $preguntasAgregadas= $this->database->query($sql);
        return $preguntasAgregadas;
    }


}