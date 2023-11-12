<?php

class UserController
{
    private $userModel;
    private $renderer;

    public function __construct($userModel, $renderer) {
        $this->userModel = $userModel;
        $this->renderer = $renderer;
    }

    public function mostrar(){
        $this->renderer->render('register');
    }

    public function login(){
        $this->renderer->render('login');
    }

    public function errorLogin($data){
        $this->renderer->render('login', $data);
    }

    public function register($datos){
        $this->renderer->render('register', $datos);
    }

    public function ingresar(){
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        if($this->userModel->checkearLogin($usuario, $password)) {
            $_SESSION["usuario"] = $usuario;
            if($this->userModel->checkearSiEsAdmin($usuario)){
                $_SESSION["admin"] = true;
                $this->renderer->render('admin');
                exit();
            }
            else{
            $user = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($usuario);
            $numeroRanking = $this->userModel->getNumeroRanking($user['username']);
            $data = [
                'username' => $user['username'],
                'image' => $user['image'],
                 'esEditor' => $user['esEditor'],
                'numeroRanking' => $numeroRanking
            ];
            $this->renderer->render('home', $data);
            exit();
        }}

        $data = array("suceso" => "Usuario o contraseña incorrectos");
        $this->errorLogin($data);

    }

    public function registrar(){
        $imagen_ruta="../public/images/generica.png";

        if(isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK){
            $imagen= $_FILES["imagen"]["name"];
            $loc_temp = $_FILES["imagen"]["tmp_name"];
            $imagen_ruta = "public/images/" . $imagen;
            move_uploaded_file($loc_temp, $imagen_ruta);
        };

        $datos = array(
            "nombre" => $_POST["nombre"],
            "fecha" => $_POST["fecha"],
            "sexo" => $_POST["sexo"],
            "email" => $_POST["email"],
            "password" => $_POST["password"],
            "usuario" => $_POST["usuario"],
            "repeatPassword" => $_POST["repeatPassword"],
            "imagen" => $imagen_ruta,
            "lat" => $_POST["lat"],
            "lon" => $_POST["lon"],
            "qr" => $this->userModel->generarQr($_POST["usuario"])
        );

        $suceso = $this->userModel->registrar($datos);

        if($suceso != "exito"){
            $data = array("suceso" => $suceso);
            $this->register($data);
            exit();
        }else {
            $this->renderer->render('login');
            exit();
        }
    }

    public function mostrarPerfil(){
        $nombre = "";
        $editar=false;
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        if($_GET['user'] != $_SESSION['usuario']){
            $nombre = $_GET['user'];
            $usuarioPerfil = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($nombre);
        }else{
            $nombre=$_SESSION['usuario'];
            $editar = true;
            $usuarioPerfil = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($nombre);
        }
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        if($usuarioPerfil['esEditor'] || $usuarioPerfil['esAdmin']){
            $this->irAlHome($usuarioPerfil, $numeroRanking);
            exit();
        }
        $data = [
            'image' => $usuario['image'],
            'username' => $usuario['username'],
            'numeroRanking' => $numeroRanking,
            'imagePerfil' =>$usuarioPerfil['image'],
            'usernamePerfil' =>$usuarioPerfil['username'],
            'puntaje' => $usuarioPerfil['puntaje'],
            'partidasRealizadas' => $usuarioPerfil['partidasRealizadas'],
            'editar' => $editar,
            'latitud' => $usuarioPerfil['latitud'],
            'longitud' => $usuarioPerfil['longitud'],
            'verificado' => $usuarioPerfil['esta_verificado'],
            'qr'=>$usuarioPerfil['qr']

        ];
        $this->renderer->render('user', $data);
    }

    public function irAlHome($usuario, $numeroRanking){
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('home', $data);
    }

    public function buscarPerfil(){
        if(!isset($_SESSION['usuario'])){
            $this->renderer->render('login');
        }
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $usernameBuscado = $_POST['username'];
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $numeroDePagina = $_GET['numeroDePagina'];
        $resultadoBusqueda = $this->userModel->buscarPrefilPorNombre($usernameBuscado, $numeroDePagina);
        if($resultadoBusqueda){
            $anterior = $this->verSiHayPaginaAnterior($numeroDePagina);
            $siguiente = $this->userModel->verSiHayPaginaSiguiente($numeroDePagina, $usernameBuscado);
            $data = [
                'username' => $usuario['username'],
                'image' => $usuario['image'],
                'resultadoBusqueda' => $resultadoBusqueda,
                'numeroRanking' => $numeroRanking,
                'siguiente' => $siguiente,
                'anterior' => $anterior,
                'usuarioBuscado' => $usernameBuscado
            ];
            $this->renderer->render('userSearch',$data);
        }
        else{
            $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
            $data = [
                'username' => $usuario['username'],
                'image' => $usuario['image'],
                'esEditor' => $usuario['esEditor'],
                'esAdmin' => $usuario['esAdmin'],
                'numeroRanking' => $numeroRanking
            ];
            $this->renderer->render('home', $data);
        }
    }

    public function verSiHayPaginaAnterior($numeroDePagina){
        if($numeroDePagina > 1){
            return $numeroDePagina - 1;
        }
        else {
            return false;
        }
    }

    public function verify(){
        $token = $_GET['token'];
        $bool = $this->userModel->verificar($token);
        if(isset($_SESSION["usuario"])){
            $user = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION["usuario"]);
            $data = [
                'username' => $user['username'],
                'image' => $user['image'],
                'success' => $bool,
                'its' => $bool
            ];
        }else {
            $data = [
                'success' => $bool
            ];
        }
        $this->renderer->render('verify', $data);
    }

    public function enviarCorreo(){
        $user = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION["usuario"]);
        $this->userModel->enviarMail($this->userModel->getLink($user['token_verificacion']), $user['mail'], $user['name']);
        $this->mostrarPerfil();
    }

    public function logout(){
        if (isset($_POST["Logout"])) {
            unset($_SESSION["usuario"]);
            $this->renderer->render('login');
            exit();
        }
    }

    public function traerUsuariosPorPuntajeAjax(){
        $users = $this->userModel->agarrarUsuariosOrdenadosPorPuntaje();
        header('Content-Type: application/json');
        echo json_encode(['usuarios' => $users]);

    }

    public function mostrarModificarPerfil(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $data=[
            'username' => $usuario['username'],
            'image' => $usuario['image'],
        ];
        $this->renderer->render('modificarPerfil', $data);
    }

    public function modificarPerfil() {
        $usuario=$_SESSION['usuario'];
        $usernameNuevo = $_POST["username"];
        $passwordNuevo= $_POST["password"];
        if(isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK){
            $imagen= $_FILES["imagen"]["name"];
            $loc_temp = $_FILES["imagen"]["tmp_name"];
            $imagen_ruta = "public/images/" . $imagen;
            move_uploaded_file($loc_temp, $imagen_ruta);
            $this->userModel->modificarImagen($imagen_ruta, $usuario);
        };

        if (strlen($passwordNuevo) > 7) {
            $this->userModel->modificarPassword($passwordNuevo,$usuario);
        }else if(strlen($passwordNuevo)>0){ $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
            $data=[
                'errorNombre' => "Contraseña muy corta",
                'username' => $usuario['username'],
                'image' => $usuario['image'],
            ];
            $this->renderer->render('modificarPerfil', $data);}

        if (strlen($usernameNuevo) > 1) {
            if(!$this->userModel->modificarUsername($usernameNuevo, $usuario)){
                $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
                $data=[
                    'errorNombre' => "Usuario ya existente",
                    'username' => $usuario['username'],
                    'image' => $usuario['image'],
                ];
                $this->renderer->render('modificarPerfil', $data);
            };
            $_SESSION["usuario"] = $usernameNuevo;
            Logger::info($_SESSION['usuario'] . "----------------");
        }
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'esAdmin' => $usuario['esAdmin'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('home', $data);
    }

}