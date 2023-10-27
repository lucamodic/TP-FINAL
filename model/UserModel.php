<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'third-party/PHPMailer-master/src/Exception.php';
require 'third-party/PHPMailer-master/src/PHPMailer.php';
require 'third-party/PHPMailer-master/src/SMTP.php';
require 'third-party/phpqrcode/qrlib.php';



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
        $latitud=$data['lat'];
        $longitud = $data['lon'];
        $token = bin2hex(random_bytes(50));
        $qr=$data['qr'];

        $sql = "INSERT INTO user (username, name, spawn, sex, mail, password, image, puntaje,partidasRealizadas, qr, latitud, longitud, esEditor, esAdmin, token_verificacion) 
            values ('$username', '$name', '$spawn', '$sex', '$mail', '$password', '$foto', 0, 0, '$qr', '$latitud', '$longitud', false, false, '$token')";
        $this->database->execute($sql);

        $verificationLink = $this->getLink($token);

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

    public function buscarPrefilPorNombre($usernameBuscado){
        if(empty($usernameBuscado)){
            return false;
        } else{
            $sql = "SELECT * FROM user WHERE username LIKE '%$usernameBuscado%'";
            return  $this->database->query($sql);
        }
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
        $sql = "UPDATE user 
                SET veces_acertadas = veces_acertadas + 1 
                WHERE username LIKE '$username'";
        $this->database->execute($sql);
    }
    public function sumarPartidaRealizadas($username){
        $sql = "UPDATE user
                SET partidasRealizadas = partidasRealizadas + 1
                WHERE username = '$username'";
        $this->database->execute($sql);
    }

    public function buscarPerfilPorNombreUsuario($username){
        $sql =  "SELECT * FROM user WHERE username LIKE '%$username%'";
        return $this->database->query($sql);
    }

    public function getLink($token){
        $port = $_SERVER['SERVER_PORT'];
        $portString = ":80";
        $default_https_port = 443;

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

        if (($protocol === 'http' && $port !== $portString) || ($protocol === 'https' && $port !== $default_https_port)) {
            $portString = ":" . $port;
        } else {
            $portString = "";
        }

        return $verificationLink ="http://" . $_SERVER['SERVER_NAME'] . $portString . "/user/verify?token=" . $token;
    }

    public function verificar($token){
        $query = "SELECT username FROM user WHERE token_verificacion = '$token' AND esta_verificado = 0";
        if ($this->database->querySinFetchAll($query) == 1) {
            $query = "UPDATE user SET esta_verificado = 1, token_verificacion = 'null' WHERE token_verificacion = '$token'";
            $this->database->execute($query);
            return true;
        } else {
            return false;
        }
    }


    public function enviarMail($verificationLink, $address, $name){
            $mail = new PHPMailer;

        try {

            $mail->IsSMTP(); // enable SMTP
            //$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465; // or 587
            $mail->IsHTML(true);

            $mail->Username = "questionariogame@gmail.com";
            $mail->Password = "sokg hfci ciwm bzmt";

            $mail->setFrom('questionariogame@gmail.com', 'Questionario');
            $mail->addAddress($address, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Verifacion de correo QUESTIONARIO';

            $mail->Body = '<h1> Link para verificar tu correo </h1>
                         Hace click aca <a href="' . $verificationLink . '"> HAZ CLICK AQUI </a>';

            $mail->send();

        } catch (Exception $e) {
            echo "Mailer Error: ".$mail->ErrorInfo;
        }
    }

    public function checkVerification($usuario){
        return $this->getUserFromDatabaseWhereUsernameExists($usuario)['esta_verificado'];
    }
    public function generarQr($username){

        $dir = 'public/images/qr/';
        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }
        $filename  = $dir. $username. '.png';
        $tamanio = 10;
        $level = 'M';
        $fraimSize = 3;
        $contenido = "http://localhost/user/mostrarPerfil?user=$username";
        QRcode::png($contenido, $filename, $level, $tamanio, $fraimSize);
        return $filename;
    }

    public function getDifficulty($usuario){
        $user = $this->getUserFromDatabaseWhereUsernameExists($usuario);
        if($user['veces_respondidas'] >= 10){
            return $user['veces_acertadas'] * 100 / $user['veces_respondidas'];
        }
        return 1000;
    }

    public function addRespondida($usuario){
        $sql = "UPDATE user SET veces_respondidas = veces_respondidas + 1 WHERE username LIKE '$usuario'";
        $this->database->execute($sql);
    }
}