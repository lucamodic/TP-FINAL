<?php

class QuestionModel{

    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function getRandomQuestion($dif) {
        if($dif != 1000){
            return $this->getPreguntaByDif($dif);
        }
        $sql = "SELECT * FROM pregunta WHERE agregada=0 AND preg_default = 1";
        $resultado = $this->database->query($sql);
        $random = rand(0, sizeof($resultado)-1);
        return $resultado[$random];
    }

    public function getPreguntaByDif($dif){
        if($dif >= 50){
            return $this->getPreguntaDificil();
        }
        return $this->getPreguntaFacil();
    }

    public function getPreguntaDificil(){
        $sql = "SELECT * FROM pregunta WHERE agregada=0 AND veces_respondida_bien * 100 / veces_respondida < 30 AND preg_default = 0";
        $resultado = $this->database->query($sql);
        $random = rand(0, sizeof($resultado)-1);
        if(sizeof($resultado) > 0){
            return $resultado[$random];
        }
    }
    public function getPreguntaFacil(){
        $sql = "SELECT * FROM pregunta WHERE agregada=0 AND veces_respondida_bien * 100 / veces_respondida >= 30 AND preg_default = 0";
        $resultado = $this->database->query($sql);
        $random = rand(0, sizeof($resultado)-1);
        if(sizeof($resultado) > 0){
            return $resultado[$random];
        }
        return $this->getPreguntaDificil();
    }

    public function getQuestionsAskedNoob($usuario){
        $sql = "SELECT * FROM preguntas_usadas pu
         JOIN pregunta p ON pu.pregunta_id = p.id
         WHERE pu.username LIKE '$usuario'
         AND p.preg_default = true";
        return sizeof($this->database->query($sql));
    }

    public function getQuestionsNoob(){
        $sql = "SELECT * FROM pregunta p WHERE p.preg_default = true";
        return sizeof($this->database->query($sql));
    }

    public function deleteUserAnsweredQuestionsNoob($usuario){
        $sql = "DELETE FROM preguntas_usadas
        WHERE username = '$usuario'
        AND pregunta_id IN (SELECT id FROM pregunta WHERE preg_default = true)";
        $this->database->execute($sql);
    }

    public function getQuestionsAskedHard($usuario){
        $sql = "SELECT * FROM preguntas_usadas pu
         JOIN pregunta p ON pu.pregunta_id = p.id
         WHERE pu.username LIKE '$usuario'
         AND p.agregada=1 AND p.veces_respondida_bien * 100 / p.veces_respondida < 30
         AND p.preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function getQuestionsHard(){
        $sql = "SELECT * FROM pregunta p 
         WHERE  p.agregada=1 AND p.veces_respondida_bien * 100 / p.veces_respondida < 30
         AND p.preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function deleteUserAnsweredQuestionsHard($usuario){
        $sql = "DELETE pu FROM preguntas_usadas pu
        JOIN pregunta p ON pu.pregunta_id = p.id
        WHERE pu.username LIKE '$usuario'
        AND p.agregada = 0
        AND (p.veces_respondida_bien * 100 / p.veces_respondida) < 30
       AND p.preg_default = false";
        $this->database->execute($sql);
    }

    public function getQuestionsAskedEasy($usuario){
        $sql = "SELECT * FROM preguntas_usadas pu
         JOIN pregunta p ON pu.pregunta_id = p.id
         WHERE pu.username LIKE '$usuario'
         AND p.agregada=0 AND p.veces_respondida_bien * 100 / p.veces_respondida >= 30
         AND p.preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function getQuestionsEasy(){
        $sql = "SELECT * FROM pregunta p 
         WHERE  p.agregada=1 AND p.veces_respondida_bien * 100 / p.veces_respondida >= 30
         AND p.preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function deleteUserAnsweredQuestionsEasy($usuario){
        $sql = "DELETE pu FROM preguntas_usadas pu
        JOIN pregunta p ON pu.pregunta_id = p.id
        WHERE pu.username LIKE '$usuario'
        AND p.agregada = 1
        AND (p.veces_respondida_bien * 100 / p.veces_respondida) >= 30
       AND p.preg_default = false";
        $this->database->execute($sql);
    }

    public function addQuestionToAnswered($pregunta, $usuario){
        $sql = "INSERT INTO preguntas_usadas (username, pregunta_id) values ('$usuario', '$pregunta')";
        $this->database->execute($sql);
        $sql = "UPDATE pregunta SET veces_respondida = veces_respondida + 1 WHERE id = '$pregunta'";
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

    public function getQuestionsAskedNotDefault($usuario){
        $sql = "SELECT * FROM preguntas_usadas pu
         JOIN pregunta p ON pu.pregunta_id = p.id
         WHERE pu.username LIKE '$usuario'
         AND p.preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function getQuestions(){
        $sql = "SELECT * FROM pregunta";
        return sizeof($this->database->query($sql));
    }

    public function agarrarUltimaPregunta($usuario){
        $result = $this->buscarPreguntaActual($usuario);
        return $this->getPreguntaById($result[0]['pregunta_id']);
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
        $sql="SELECT nombre FROM categoria";
        return $this->database->query($sql);
    }

    public function setPreguntasAgregadas($data){
        $enunciado=$data["enunciado"];
        $categoriaNombre=$data["categoria"];
        $sql2="SELECT id FROM categoria WHERE nombre='$categoriaNombre'";
        $categoria= intval($this->database->query($sql2));
        $respuesta1=$data["respuesta1"];
        $respuesta2=$data["respuesta2"];
        $respuesta3=$data["respuesta3"];
        $respuesta4=$data["respuesta4"];
        $sql = "INSERT INTO pregunta(id_categoria, enunciado, dificultad, reportada, agregada, veces_respondida)
        values('$categoria', '$enunciado', 'facil', false, true, 0);";
        $this->database->execute($sql);

        $sql7="SELECT id FROM pregunta WHERE enunciado='$enunciado'";
        $id= intval($this->database->query($sql7));

        $sql3 = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta1', '$id', false);";
        $this->database->execute($sql3);
        $sql4 = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta2', '$id', false);";
        $this->database->execute($sql4);
        $sql5 = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta3', '$id', false);";
        $this->database->execute($sql5);
        $sql6 = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta4', '$id', true);";
        $this->database->execute($sql6);

    }
    public function getPreguntasNuevas(){
        $sql = "SELECT * FROM pregunta WHERE agregada = 1";
        $preguntasAgregadas = $this->database->query($sql);
        return $preguntasAgregadas;
    }
    public function getRespuestasNuevas(){
        $sql = "SELECT * FROM pregunta WHERE agregada = 1";
        $preguntasAgregadas = $this->database->query($sql);
        $respuestasAgregadas = array();
        foreach($preguntasAgregadas as $pregunta){
            $preguntaId = $pregunta['id'];
            $sql2 ="SELECT * FROM respuesta WHERE id_pregunta = '$preguntaId'";
            $respuestas = $this->database->query($sql2);
            $respuestasAgregadas = array_merge($respuestasAgregadas, $respuestas);
        }
        return $respuestasAgregadas;
    }

    public function eliminarReportada($id){
        $sql="DELETE FROM pregunta WHERE id='$id'";
        $this->database->execute($sql);
        $sql2="DELETE FROM respuesta WHERE id_pregunta='$id'";
        $this->database->execute($sql2);
    }
    public function reestablecerReportada($id){
        $sql = "UPDATE pregunta SET reportada = 0 WHERE id = '$id'";
        $this->database->execute($sql);
    }

    public function eliminarNueva($id){
        $sql="DELETE FROM pregunta WHERE id='$id'";
        $this->database->execute($sql);
        $sql2="DELETE FROM respuesta WHERE id_pregunta='$id'";
        $this->database->execute($sql2);
    }
    public function aceptarNueva($id){
        $sql = "UPDATE pregunta SET agregada = 0 WHERE id = '$id'";
        $this->database->execute($sql);
    }
    public function setNuevaCategoria($categoria){
        $sql = "INSERT INTO categoria(nombre,agregada)
        values('$categoria',1)";
        $this->database->execute($sql);
    }
    public function getCategoriasNuevas(){
        $sql = "SELECT * FROM categoria WHERE agregada = 1";
        return $this->database->query($sql);
    }
    public function eliminarNuevaCategoria($id){
        $sql="DELETE FROM categoria WHERE id='$id'";
        $this->database->execute($sql);
    }
    public function aceptarNuevaCategoria($id){
        $sql = "UPDATE categoria SET agregada = 0 WHERE id = '$id'";
        $this->database->execute($sql);
    }

    public function sumarAcertada($id){
        $sql = "UPDATE pregunta 
                SET veces_respondida_bien = veces_respondida_bien + 1 
                WHERE id LIKE '$id'";
        $this->database->execute($sql);
    }

    public function getQuestionsAvailable(){
        $sql = "SELECT * FROM pregunta WHERE agregada = true AND preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function deleteAllQuestions($username){
        $sql = "DELETE FROM preguntas_usadas WHERE username LIKE '$username'";
        return $this->database->execute($sql);
    }

    public function checkAllQuestions($user){
        if($user['veces_respondidas'] = 10){
            $this->deleteUserAnsweredQuestionsNoob($user['username']);
        }
        if($this->getQuestionsAvailable() == $this->getQuestionsAskedNotDefault($user['username'])){
            $this->deleteAllQuestions($user['username']);
        }
    }

}