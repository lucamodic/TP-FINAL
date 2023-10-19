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
                'image' => $user['image']
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
            "imagen" => $imagen_ruta

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
    public function cambiarImagen(){
        $imagen=$_POST['imagen'];
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        this->userModel->cambiarImagen($imagen, $usuario);
        //COMO HACER QUE SE REDIRIGA AL METODO MOSTRARUSUARIO?
    }

}