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
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'esAdmin' => $usuario['esAdmin'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('home', $data);
    }
    public function mostrarRanking(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $usuarios = $this->userModel->getAllUsersOrdenados();
        $partidas = $this->userModel->buscarPartidas( $usuario['username']);
        $data = [
            'usuarios' => $usuarios,
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'partidas' =>$partidas
        ];
        $this->renderer->render('ranking', $data);
    }
    public function mostrarPreguntasReportadas(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $preguntas = $this->questionModel->getPreguntasReportadas();
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'preguntasReportadas' => $preguntas
        ];
        $this->renderer->render('preguntasReportadas', $data);
    }

    public function mostrarAgregarPregunta(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $categorias= $this->questionModel->getCategorias();
        $data=[
            'categorias'=> $categorias,
             'username' => $usuario['username'],
             'image' => $usuario['image']
        ];
        $this->renderer->render('agregarPreguntas',$data);
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
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $dataHome = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'esAdmin' => $usuario['esAdmin']
        ];
        $this->renderer->render('home', $dataHome);
    }

    public function mostrarPreguntasNuevas(){
        $preguntas = $this->questionModel->getPreguntasNuevas();
        $respuestas = $this->questionModel->getRespuestasNuevas();
        $categorias=$this->questionModel->getCategoriasNuevas();
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'preguntasNuevas' => $preguntas,
            'respuestasNuevas' => $respuestas,
            'categoriasNuevas' => $categorias
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
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'esAdmin' => $usuario['esAdmin']
        ];
        $this->renderer->render('home', $data);
    }
    public function eliminarAgregarNuevas(){
        $eliminar = isset($_POST["eliminar"]);
        $agregar = isset($_POST["agregar"]);
        $pregunta_id=$_POST['pregunta_id'];
        if($eliminar){
            $this->questionModel->eliminar($pregunta_id);
        }else if($agregar){
            $this->questionModel->aceptarNueva($pregunta_id);
        }
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'esAdmin' => $usuario['esAdmin']
        ];
        $this->renderer->render('home', $data);
    }
    public function mostrarAgregarCategoria(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $data=[
            'username' => $usuario['username'],
            'image' => $usuario['image']
        ];
        $this->renderer->render('agregarCategoria',$data);
    }
    public function agregarCategoria(){
        $categoria=$_POST["categoria"];
        $this->questionModel->setNuevaCategoria($categoria);
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $dataHome = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'esAdmin' => $usuario['esAdmin']
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
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'esAdmin' => $usuario['esAdmin']
        ];
        $this->renderer->render('home', $data);
    }
    public function mostrarModoEditor(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $preguntas=$this->questionModel->mostrarTodasLasPreguntas();
        $data=[
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'preguntas'=> $preguntas,
        ];
        $this->renderer->render('modoEditor',$data);
    }
    public function editarEliminar(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $eliminar = isset($_POST["eliminar"]);
        $editar = isset($_POST["editar"]);
        $pregunta_id=$_POST['pregunta_id'];
        if($eliminar){
            $this->questionModel->eliminar($pregunta_id);
            $data = [
                'username' => $usuario['username'],
                'image' => $usuario['image'],
                'esEditor' => $usuario['esEditor'],
                'esAdmin' => $usuario['esAdmin']
            ];
            $this->renderer->render('home', $data);
        }else if($editar){
            $pregunta=$this->questionModel->buscarPreguntaParaEditar($pregunta_id);
            $categorias= $this->questionModel->getCategorias();
            $data=[
                'username' => $usuario['username'],
                'image' => $usuario['image'],
                'pregunta'=>$pregunta,
                'categorias' => $categorias
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
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $dataHome = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'esEditor' => $usuario['esEditor'],
            'esAdmin' => $usuario['esAdmin']
        ];
        $this->renderer->render('home', $dataHome);
    }
}