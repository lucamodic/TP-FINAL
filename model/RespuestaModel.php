<?php

class RespuestaModel{

    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function getRespuestas($idPregunta){
        $sql = "SELECT * FROM respuesta WHERE id_pregunta LIKE '$idPregunta'  ORDER BY RAND()";
        return $this->database->query($sql);
    }

    public function getRespuestasEditor($idPregunta){
        $sql = "SELECT * FROM respuesta WHERE id_pregunta LIKE '$idPregunta'";
        return $this->database->query($sql);
    }

    public function editarRespuestas($data){
        $texto1 = $data['respuesta1'];
        if($texto1){
            $sql = "UPDATE respuesta SET texto='$texto1' WHERE texto LIKE '" . $data['respuestasOriginales'][0]['texto'] . "'";
            $this->database->execute($sql);
        }
        $texto2 = $data['respuesta2'];
        if($texto2){
            $sql = "UPDATE respuesta SET texto='$texto2' WHERE texto LIKE '" . $data['respuestasOriginales'][1]['texto'] . "'";
            $this->database->execute($sql);
        }
        $texto3 = $data['respuesta3'];
        if($texto3){
            $sql = "UPDATE respuesta SET texto='$texto3' WHERE texto LIKE '" . $data['respuestasOriginales'][2]['texto'] . "'";
            $this->database->execute($sql);
        }
        $texto4 = $data['respuesta4'];
        if($texto4){
            $sql = "UPDATE respuesta SET texto='$texto4' WHERE texto LIKE '" . $data['respuestasOriginales'][3]['texto'] . "'";
            $this->database->execute($sql);
        }

    }

}