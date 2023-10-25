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
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'esAdmin' => $usuario['esAdmin']
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
    public function mostrarPreguntasReportadas(){
        $preguntas = $this->questionModel->getPreguntasReportadas();
        $data = [
            'preguntasReportadas' => $preguntas
        ];
        $this->renderer->render('preguntasReportadas', $data);
    }
    public function mostrarAgregarPregunta(){
        $categorias= $this->questionModel->getCategorias();
        $data=[
            'categorias'=> $categorias
        ];
        $this->renderer->render('agregarPreguntas',$data);
    }

    public function agregarPreguntaParaEditor(){
        $data = array(
            "categoriaNueva"=> $_POST["categoriaNueva"],
            "categoria" => $_POST["categoria"],
            "enunciado" => $_POST["enunciado"],
            "respuesta1" => $_POST["respuesta1"],
            "respuesta2" => $_POST["respuesta2"],
            "respuesta3" => $_POST["respuesta3"],
            "respuesta4" => $_POST["respuesta4"]
            );
        $this->questionModel->setPreguntasAgregadas($data);
        $this->renderer->render('home');
    }

    public function mostrarPreguntasNuevas(){
        $preguntas = $this->questionModel->getPreguntasNuevas();
        $data = [
            'preguntasNuevas' => $preguntas
        ];
        $this->renderer->render('preguntasNuevas', $data);
    }
}