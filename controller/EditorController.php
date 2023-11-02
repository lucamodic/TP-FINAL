<?php

class EditorController
{
    private $renderer;
    private $userModel;
    private $questionModel;

    public function __construct($userModel, $renderer, $questionModel) {
        $this->userModel = $userModel;
        $this->renderer = $renderer;
        $this->questionModel = $questionModel;
    }

    public function mostrarPreguntasReportadas(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $preguntas = $this->questionModel->getPreguntasReportadas();
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'preguntasReportadas' => $preguntas,
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('preguntasReportadas', $data);
    }

    public function agregarPreguntaParaEditor(){

        $data = array(
            "categoria" => $_POST["categoria"],
            "enunciado" => $_POST["enunciado"],
            "respuesta1" => $_POST["respuesta1"],
            "respuesta2" => $_POST["respuesta2"],
            "respuesta3" => $_POST["respuesta3"],
            "respuesta4" => $_POST["respuesta4"]
        );
        $this->questionModel->setPreguntasAgregadas($data);
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $dataHome = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('home', $dataHome);
    }

    public function mostrarPreguntasNuevas(){
        $preguntas = $this->questionModel->getPreguntasNuevas();
        $preguntasConRespuestas= $this->questionModel->getRespuestasNuevas();
        $categorias = $this->questionModel->getCategoriasNuevas();
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'preguntasNuevas' => $preguntas,
            'categoriasNuevas' => $categorias,
            'numeroRanking' => $numeroRanking,
            'preguntasConRespuestas' => $preguntasConRespuestas
        ];

        $this->renderer->render('preguntasNuevas', $data);
    }

    public function eliminarReestablecerReportadas(){
        $eliminar = isset($_POST["eliminar"]);
        $reestablecer = isset($_POST["reestablecer"]);
        $pregunta_id=$_POST['pregunta_id'];
        if($eliminar){
            $this->questionModel->eliminar($pregunta_id);
        }else if($reestablecer){
            $this->questionModel->reestablecerReportada($pregunta_id);
        }
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

    public function eliminarAgregarNuevas(){
        $eliminar = isset($_POST["eliminar"]);
        $agregar = isset($_POST["agregar"]);
        $pregunta_id = $_POST['pregunta_id'];
        if($eliminar){
            $this->questionModel->eliminar($pregunta_id);
        }else if($agregar){
            $this->questionModel->aceptarNueva($pregunta_id);
        }
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

    public function agregarCategoria(){
        $categoria=$_POST["categoria"];
        $color=$_POST["color"];
        $this->questionModel->setNuevaCategoria($categoria,$color);
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $dataHome = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('home', $dataHome);
    }

    public function eliminarAgregarNuevasCategorias(){
        $eliminar = isset($_POST["eliminar"]);
        $agregar = isset($_POST["agregar"]);
        $id=$_POST['id'];
        if($eliminar){
            $this->questionModel->eliminarNuevaCategoria($id);
        }else if($agregar){
            $this->questionModel->aceptarNuevaCategoria($id);
        }
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

    public function mostrarModoEditor(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $preguntasConRespuestas = $this->questionModel->getPreguntasEditor();

        $data=[
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'numeroRanking' => $numeroRanking,
            'preguntasConRespuestas'=> $preguntasConRespuestas,

        ];
        $this->renderer->render('modoEditor',$data);
    }

    public function editarEliminar(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $eliminar = isset($_POST["eliminar"]);
        $editar = isset($_POST["editar"]);
        $pregunta_id=$_POST['pregunta_id'];
        if($eliminar){
            $this->questionModel->eliminar($pregunta_id);
            $data = [
                'username' => $usuario['username'],
                'image' => $usuario['image'],
                'esEditor' => $usuario['esEditor'],
                'numeroRanking' => $numeroRanking
            ];
            $this->renderer->render('home', $data);
        }else if($editar){
            $pregunta=$this->questionModel->buscarPreguntaParaEditar($pregunta_id);
            $categorias= $this->questionModel->getCategoriasQueEstenAgregadas();
            $data=[
                'username' => $usuario['username'],
                'image' => $usuario['image'],
                'pregunta'=>$pregunta,
                'categorias' => $categorias,
                'numeroRanking' => $numeroRanking
            ];
            $this->renderer->render('editar', $data);
        }
    }

    public function editarPregunta(){
        $data = array(
            "id"=>$_POST["id"],
            "categoria" =>$_POST["categoria"],
            "enunciado" =>$_POST["enunciado"],
            "respuesta1" =>$_POST["respuesta1"],
            "respuesta2" =>$_POST["respuesta2"],
            "respuesta3" =>$_POST["respuesta3"],
            "respuesta4" =>$_POST["respuesta4"]
        );
        $this->questionModel->editarPregunta($data);
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $dataHome = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('home', $dataHome);
    }
}