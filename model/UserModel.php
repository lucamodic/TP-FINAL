<?php

class UserModel{
    private $database;



    public function __construct($database) {
        $this->database = $database;
    }
    public function add($datos){
        $suceso = $this->checkdata($datos);

        if($suceso == "exito"){
            $this->addToDatabase($datos);
        }
        return $suceso;
    }



    public function addToDatabase($data){
        $name = $data['nombre'];
        $spawn = $data['fecha'];
        $sex = $data['sexo'];
        $mail = $data['email'];
        $password = $data['password'];
        $foto = $data['imagen'];
        $username = $data['usuario'];

        $sql = "INSERT INTO user (name, spawn, sex, mail, password, username, image) 
            values ('$name', '$spawn', '$sex', '$mail', '$password','$username', '$foto')";
        $this->database->execute($sql);
    }

    public function compararContrasenia($data){
        if ($data['password'] != $data['repeatPassword']) {
            return false;
        }
        return true;
    }

    public function verificarLongitud($data){
        if(strlen($data['password']) < 8){
            return false;
        }
        return true;
    }

    public function checkdata($data){
        foreach ($data as $dato){
            if(empty($dato)){
                return "Completar todos los campos";
            }
        }
        if(!$this->verificarLongitud($data)){
            return "Contraseña muy corta";
        }
        if(!$this->compararContrasenia($data)){
            return "Contraseñas no coinciden";
        }

        $sql = "SELECT MAIL FROM user";
        $resultado = $this->database->query($sql);

        foreach ($resultado as $mail){
            if($mail == $data['mail']){
                return "Mail ya registrado";
            }
        }
         return "exito";
    }
}