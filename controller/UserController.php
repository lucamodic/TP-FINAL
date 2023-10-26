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
            $user = $this->userModel->getUserFromDatabaseWhereUsernameExists($usuario);
            $data = [
                'username' => $user['username'],
                'image' => $user['image'],
                 'esEditor' => $user['esEditor'],
                 'esAdmin' => $user['esAdmin']
            ];
            $this->renderer->render('home', $data);
            exit();
        }

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
        $nombre = $_SESSION['usuario'];
        if(isset($_GET['user'])){
            $nombre = $_GET['user'];
        }
        $editar = false;
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($nombre);
        if($nombre === $_SESSION['usuario']){
            $editar = true;
        }

        $data = [
            'image' => $usuario['image'],
            'username' => $usuario['username'],
            'puntaje' => $usuario['puntaje'],
            'partidasRealizadas' => $usuario['partidasRealizadas'],
            'editar' => $editar,
            'latitud' => $usuario['latitud'],
            'longitud' => $usuario['longitud'],
            'verificado' => $usuario['esta_verificado'],
            'qr'=>$usuario['qr']
        ];
        $this->renderer->render('user', $data);
    }
    public function buscarPerfil(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $usernameBuscado = $_POST['username'];
        $resultadoBusqueda = $this->userModel->getUserFromDatabaseWhereUsernameExists($usernameBuscado);
        if($resultadoBusqueda){
            $data = [
                'username' => $usuario['username'],
                'image' => $usuario['image'],
                'imagenBuscado' => $resultadoBusqueda['image'],
                'usernameBuscado' => $resultadoBusqueda['username'],
                'puntaje' => $resultadoBusqueda['puntaje'],
                'partidasRealizadas' => $resultadoBusqueda['partidasRealizadas'],
                'latitud' => $resultadoBusqueda['latitud'],
                'longitud' => $resultadoBusqueda['longitud']
            ];
            $this->renderer->render('userSearch',$data);
        }
        else{

            $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
            $data = [
                'username' => $usuario['username'],
                'image' => $usuario['image'],
                'esEditor' => $usuario['esEditor'],
                'esAdmin' => $usuario['esAdmin']
            ];
            $this->renderer->render('home', $data);
        }
    }

    public function verify(){
        $token = $_GET['token'];
        $bool = $this->userModel->verificar($token);
        $user = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION["usuario"]);
        $data = [
            'username' => $user['username'],
            'image' => $user['image'],
            'success' => $bool,
            'its' => $bool
        ];
        $this->renderer->render('verify', $data);
    }

    public function enviarCorreo(){
        $user = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION["usuario"]);
        $this->userModel->enviarMail($this->userModel->getLink($user['token_verificacion']), $user['mail'], $user['name']);
        $this->mostrarPerfil();
    }
}