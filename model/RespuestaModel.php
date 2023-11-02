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

}