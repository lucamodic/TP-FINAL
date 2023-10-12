<?php

class GameController{
    private $renderer;
    private $userModel;
    private $questionModel;
    private $respuestaModel;

    public function __construct($questionModel, $respuestaModel, $userModel,  $renderer){
        $this->questionModel = $questionModel;
        $this->respuestaModel= $respuestaModel;
        $this->userModel = $userModel;
        $this->renderer = $renderer;
    }

    public function jugar(){
        $this->renderer->render('game', $this->getDataGame());
    }

    public function checkAnswer(){
        $respuesta = $_POST['bool'];
        $pregunta = $_POST['id_pregunta'];
        if($respuesta){
            $this->renderer->render('game', $this->getDataGame());
            exit();
        }
        $this->renderer->render('end', $this->getData());
        exit();
    }

    public function getDataGame(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $pregunta = $this->questionModel->getRandomQuestion();
        $respuestas = $this->respuestaModel->getRespuestas($pregunta['id']);
        return $data = [
            'pregunta' => $pregunta,
            'respuestas' => $respuestas,
            'username' => $usuario['username'],
            'image' => $usuario['image']
        ];
    }

    public function getData(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);

        return $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image']
        ];
    }

}