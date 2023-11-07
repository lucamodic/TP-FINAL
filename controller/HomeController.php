<?php

class HomeController
{
    private $renderer;
    private $userModel;
    private $questionModel;

    public function __construct($userModel, $renderer, $questionModel) {
        $this->userModel = $userModel;
        $this->renderer = $renderer;
        $this->questionModel = $questionModel;
    }

    public function mostrar(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('home', $data);
    }

    public function mostrarRanking(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $usuarios = $this->userModel->agarrarUsuariosOrdenadosPorPuntaje();
        $partidas = $this->userModel->buscarPartidas( $usuario['username']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $data = [
            'usuarios' => $usuarios,
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'partidas' =>$partidas,
             'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('ranking', $data);
    }

    public function mostrarAgregarPregunta(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $categorias = $this->questionModel->getCategoriasQueEstenAgregadas();
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $data=[
            'categorias'=> $categorias,
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('agregarPreguntas',$data);
    }

    public function mostrarAgregarCategoria(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $data=[
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('agregarCategoria',$data);
    }
    public function tienda(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $data=[
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('tienda',$data);
    }
}