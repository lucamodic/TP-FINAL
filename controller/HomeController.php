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


}