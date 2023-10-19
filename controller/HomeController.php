<?php

class HomeController
{
    private $renderer;
    private $userModel;

    public function __construct($userModel, $renderer) {
        $this->userModel = $userModel;
        $this->renderer = $renderer;
    }

    public function mostrar(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image']
        ];
        $this->renderer->render('home', $data);
    }
    public function mostrarRanking(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $usuarios = $this->userModel->getAllUsersOrdenados();
        $data = [
            'usuarios' => $usuarios,
            'username' => $usuario['username'],
            'image' => $usuario['image']
        ];
        $this->renderer->render('ranking', $data);
    }
    public function mostrarPerfil(){
        $nombre=$_GET['user'];
        $editar=false;
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        if($nombre === $usuario['username']){
         $editar = true;
        }
        $data = [
            'image' => $usuario['image'],
            'username' => $usuario['username'],
            'puntaje' => $usuario['puntaje'],
            'partidasRealizadas' => $usuario['partidasRealizadas'],
            'editar' => $editar
        ];
        $this->renderer->render('user', $data);
    }



}