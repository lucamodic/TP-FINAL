<?php

class PartidaModel
{
    private $database;


    public function __construct($database) {
        $this->database = $database;
    }

    public function checkPartida($usuario){
        if(sizeof($this->getPartidaByUsername($usuario)) > 0){
            return true;
        };
        return false;
    }

    public function crearPartida($usuario){
        $sql = "INSERT INTO partida (username, puntaje, esta_activa, tiempo_pregunta) values ('$usuario', 0, true, 10)";
        $this->database->execute($sql);
        return $this->getPartidaByUsername($usuario);
    }

    public function getPartidaByUsername($usuario){
        $sql = "SELECT * FROM partida 
         WHERE username LIKE '$usuario'
         AND esta_activa = true";
        return $this->database->query($sql);
    }

    public function sumarPuntos($id){
        $sql = "UPDATE partida
                SET puntaje = puntaje + 1
                WHERE id = '$id'";
        $this->database->execute($sql);
    }

    public function gameOver($id){
        $sql = "UPDATE partida
                SET esta_activa = false
                WHERE id = '$id'";
        $this->database->execute($sql);
    }

    public function getPartidaById($id){
        $sql = "SELECT * FROM partida 
         WHERE id = '$id'";
        return $this->database->query($sql)[0];
    }

}