<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'third-party/PHPMailer-master/src/Exception.php';
require 'third-party/PHPMailer-master/src/PHPMailer.php';
require 'third-party/PHPMailer-master/src/SMTP.php';


class UserModel{
    private $database;


    public function __construct($database) {
        $this->database = $database;
    }

    public function add($datos, $mailer){
        $suceso = $this->checkdata($datos);

        if($suceso == "exito"){
            $this->addToDatabase($datos, $mailer);
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
        $latitud=$data['lat'];
        $longitud = $data['lon'];
        $token = bin2hex(random_bytes(50));

        $sql = "INSERT INTO user (username, name, spawn, sex, mail, password, image, puntaje,partidasRealizadas, qr, latitud, longitud, esEditor, esAdmin, token_verificacion) 
            values ('$username', '$name', '$spawn', '$sex', '$mail', '$password', '$foto', 0, 0, '', '$latitud', '$longitud', false, false, '$token')";
        $this->database->execute($sql);

        $verificationLink = "localhost/user/verify?token=" . $token;

        $this->enviarMail($verificationLink, $mail, $name);

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

    public  function  getAllUsersOrdenados(){
        $sql = "SELECT * FROM user 
        ORDER BY puntaje DESC";
        return $this->database->query($sql);
    }

    public function checkPassword($usuario, $password){
        $resultado = $this->getUserFromDatabaseWhereUsernameExists($usuario);
        return $password == $resultado['password'];
    }

    public function sumarPuntos($username){
        $sql = "UPDATE user
                SET puntaje = puntaje + 1
                WHERE username = '$username'";
        $this->database->execute($sql);
    }
    public function sumarPartidaRealizadas($username){
        $sql = "UPDATE user
                SET partidasRealizadas = partidasRealizadas + 1
                WHERE username = '$username'";
        $this->database->execute($sql);
    }

    public function cambiarImagen($imagen, $username){
        $sql = "UPDATE user
                SET image = '$imagen'
                WHERE username = '$username';";
        $this->database->execute($sql);
    }

    public function buscarPerfilPorNombreUsuario($username){
        $sql =  "SELECT * FROM user WHERE username LIKE '%$username%'";
        return $this->database->query($sql);
    }

    public function verificar($token){
        $this->UserModel->verificar($token);
        $query = "SELECT id FROM users WHERE token_verificacion = ? AND esta_verificado = 0";
        $stmt = $this->database->query($query);

        if ($stmt->rowCount() == 1) {
            // Token is valid; mark the user as verified
            $query = "UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = ?";
            $this->database->execute($query);
            echo "Email verification successful. You can now log in.";
        } else {
            echo "Invalid or expired verification link.";
        }
    }


    public function enviarMail($verificationLink, $address, $name){
            $mail = new PHPMailer;

            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'freegames4me2me@gmail.com';
            $mail->Password = 'Freegames';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;


            $mail->setFrom('freegames4me2me@gmail.com', 'Frei');
            $mail->addAddress("$address", "$name");
            $mail->Subject = 'Valida tu cuenta para disfrutar!';


            $mail->isHTML(true);
            $mail->Body = '<h1> Link para verificar tu correo </h1>
                         Hace click aca <a href="' . $verificationLink . '"> Link para validar tu correo </a>';
            $mail->send();
    }



}