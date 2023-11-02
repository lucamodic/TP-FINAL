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

    public function add(){
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

        $suceso = $this->userModel->add($datos);

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

    public function buscarPerfil(){
        if(!isset($_SESSION['usuario'])){
            $this->renderer->render('login');
        }
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $usernameBuscado = $_POST['username'];
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $resultadoBusqueda = $this->userModel->buscarPrefilPorNombre($usernameBuscado);
        if($resultadoBusqueda){
            $data = [
                'username' => $usuario['username'],
                'image' => $usuario['image'],
                'resultadoBusqueda' => $resultadoBusqueda,
                'numeroRanking' => $numeroRanking
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
}