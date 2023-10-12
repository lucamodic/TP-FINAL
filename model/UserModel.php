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

    public function checkearLogin($usuario, $password){
         if(!$this->checkUsername($usuario)){
             return false;
        }
        return $this->checkPassword($usuario, $password);
    }

    public function addToDatabase($data){
        $name = $data['nombre'];
        $spawn = $data['fecha'];
        $sex = $data['sexo'];
        $mail = $data['email'];
        $password = $data['password'];
        $foto = $data['imagen'];
        $username = $data['usuario'];

        $sql = "INSERT INTO user (username, name, spawn, sex, mail, password, image, puntaje,partidasRealizadas, qr) 
            values ('$username', '$name', '$spawn', '$sex', '$mail', '$password', '$foto', 0, 0, '')";
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

        if(!$this->checkEmail($data)){
            return "Mail ya registrado";
        }

        $usuario = $data['usuario'];

        if($this->checkUsername($usuario)){
            return "Usuario ya registrado";
        }

         return "exito";
    }

    public function checkEmail($data){
        $sql = "SELECT * FROM user ";
        $resultado = $this->database->query($sql);

        foreach ($resultado as $mail){
            if($mail['mail'] == $data['email']){
                return false;
            }
        }

        return true;
    }

    public function checkUsername($usuario){
        $sql = "SELECT * FROM user";
        $resultado = $this->database->query($sql);

        foreach ($resultado as $username){
            if($username['username'] == $usuario){
                return true;
            }
        }
        return false;
    }

    public function getUserFromDatabaseWhereUsernameExists($usuario){
        $sql = "SELECT * FROM user";
        $resultado = $this->database->query($sql);

        foreach ($resultado as $username){
            if($username['username'] == $usuario){
                return $username;
            }
        }
        return false;
    }

    public function checkPassword($usuario, $password){
        $resultado = $this->getUserFromDatabaseWhereUsernameExists($usuario);
        return $password == $resultado['password'];
    }

}