<?php

class EditorController
{
    private $renderer;
    private $userModel;
    private $questionModel;

    private $respuestaModel;

    public function __construct($userModel, $renderer, $questionModel,$respuestaModel) {
        $this->userModel = $userModel;
        $this->renderer = $renderer;
        $this->questionModel = $questionModel;
        $this->respuestaModel = $respuestaModel;
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
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $data = array(
            "categoria" => $_POST["categoria"],
            "enunciado" => $_POST["enunciado"],
            "respuesta1" => $_POST["respuesta1"],
            "respuesta2" => $_POST["respuesta2"],
            "respuesta3" => $_POST["respuesta3"],
            "respuesta4" => $_POST["respuesta4"],
            "esEditor"=>$usuario['esEditor']
        );
        $this->questionModel->setPreguntasAgregadas($data);
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
        $categoria = $_POST["categoria"];
        $color = $_POST["color"];
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $es_editor= $usuario['esEditor'];
        $this->questionModel->setNuevaCategoria($categoria, $color, $es_editor);
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
            $respuestas = $this->respuestaModel->getRespuestasEditor($pregunta_id);
            $categorias= $this->questionModel->getCategoriasQueEstenAgregadas();
            $data=[
                'username' => $usuario['username'],
                'image' => $usuario['image'],
                'pregunta'=>$pregunta,
                'categorias' => $categorias,
                'numeroRanking' => $numeroRanking,
                "respuestas" => $respuestas
            ];
            $this->renderer->render('editar', $data);
        }
    }

    public function editarPregunta(){
        $respuestas = $this->respuestaModel->getRespuestasEditor($_POST["id"]);
        $data = array(
            "id"=>$_POST["id"],
            "categoria" =>$_POST["categoria"],
            "enunciado" =>$_POST["enunciado"],
            "respuesta1" =>$_POST["respuesta1"],
            "respuesta2" =>$_POST["respuesta2"],
            "respuesta3" =>$_POST["respuesta3"],
            "respuesta4" =>$_POST["respuesta4"],
            "respuestasOriginales" => $respuestas
        );
        $this->questionModel->editarPregunta($data);
        $this->respuestaModel->editarRespuestas($data);
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