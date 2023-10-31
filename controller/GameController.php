<?php
class GameController{
    private $renderer;
    private $userModel;
    private $questionModel;
    private $respuestaModel;
    private $partidaModel;

    public function __construct($questionModel, $respuestaModel, $userModel,  $renderer, $partidaModel){
        $this->questionModel = $questionModel;
        $this->respuestaModel= $respuestaModel;
        $this->userModel = $userModel;
        $this->renderer = $renderer;
        $this->partidaModel= $partidaModel;
    }

    public function startGame(){
        if($this->userModel->checkVerification($_SESSION['usuario'])){
            $this->createSession();
            $this->renderer->render('game', $this->getDataGameStart());
        }
        else {
            $this->renderer->render('home', $this->getData());
        }
    }

    public function end(){
        $partida = $this->partidaModel->getPartidaByUsername($_SESSION['usuario'])[0];
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $this->userModel->sumarPartidaRealizadas($_SESSION['usuario']);
        $this->partidaModel->gameOver($partida['id']);
        $data = [
            'puntajeUsuario'=> $usuario['puntaje'],
            'puntajeTotal' => $partida['puntaje'],
            'username' => $usuario['username'],
            'image' => $usuario['image']
        ];
        $this->renderer->render('end', $data);
    }

    public function checkAnswer(){
        if(!isset($_POST['URL'])){
            $this->renderer->render('home', $this->getDataCheater());
            exit();
        }
        $respuesta = $_POST['bool'];
        $pregunta = $_POST['id_pregunta'];
        $tiempo = - ($_SESSION['start_time'] - time());
        if($respuesta && $tiempo <= 10){
            $this->userModel->sumarPuntos($_SESSION['usuario']);
            $this->partidaModel->sumarPuntos($_POST['id_partida']);
            $this->questionModel->sumarAcertada($pregunta);
            $this->createSession();
            $this->renderer->render('game', $this->getDataGame());
            exit();
        }
        $this->userModel->sumarPartidaRealizadas($_SESSION['usuario']);
        $this->partidaModel->gameOver($_POST['id_partida']);
        $this->renderer->render('end', $this->getDataGameOver());
        exit();
    }

    public function createSession(){
        if(isset($_SESSION['tiempo'])){
            unset($_SESSION['tiempo']);
            unset($_SESSION['start_time']);
        }
        $_SESSION['start_time'] = time();
        $_SESSION['tiempo'] = 10;
    }

    public function calcularTiempoQueQueda(){
        $tiempo = $_SESSION['tiempo'] - (time() - $_SESSION['start_time']);
        if($tiempo <= -1){
            $this->end();
        }
        $response = array("tiempo" => $tiempo);
        echo json_encode($response);
    }

    public function checkQuestion($usuario){
        $dificultadUser = $this->userModel->getDifficulty($usuario);
        $this->checkCount($usuario);
        $pregunta = $this->questionModel->getRandomQuestion($dificultadUser);
        $this->questionModel->addQuestionToAnswered($pregunta['id'], $usuario);
        $this->userModel->addRespondida($usuario);
        return $pregunta;
    }

    public function checkCount($usuario){
        $user = $this->userModel->getUserFromDatabaseWhereUsernameExists($usuario);
        $this->questionModel->checkAllQuestions($user);
    }

    public function getDataGame(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $pregunta = $this->checkQuestion($usuario['username']);
        $respuestas = $this->respuestaModel->getRespuestas($pregunta['id']);
        $partida = $_POST['id_partida'];
        $idCategoria = $pregunta['id_categoria'];
        $categoria = $this->questionModel->getColor($idCategoria);
        return $data = [
            'partida' => $partida,
            'pregunta' => $pregunta,
            'respuestas' => $respuestas,
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'categoria' => $categoria
        ];
    }

    public function getData(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        return $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'verificar' => "Verifica tu mail antes de jugar!"
        ];
    }

    public function getDataCheater(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        return $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image']
        ];
    }

    public function getDataGameStart(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $username = $usuario['username'];
        if($this->partidaModel->checkPartida($username)){
            $partida = $this->partidaModel->getPartidaByUsername($username);
            $this->end();
        }else {
            $pregunta = $this->checkQuestion($username);
            $partida = $this->partidaModel->crearPartida($username);
        }
        $respuestas = $this->respuestaModel->getRespuestas($pregunta['id']);
        $idCategoria = $pregunta['id_categoria'];
        $categoria = $this->questionModel->getColor($idCategoria);
        return $data = [
            'partida' => $partida[0]['id'],
            'pregunta' => $pregunta,
            'respuestas' => $respuestas,
            'username' => $username,
            'image' => $usuario['image'],
            'categoria' => $categoria
        ];
    }

    public function getDataGameOver(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $partida = $this->partidaModel->getPartidaById($_POST['id_partida']);
        return $data = [
            'puntajeUsuario'=> $usuario['puntaje'],
            'puntajeTotal' => $partida['puntaje'],
            'username' => $usuario['username'],
            'image' => $usuario['image']
        ];
    }

    public function reportarPregunta(){
        $idPreguntaReportada = $_GET['id_pregunta'];
        $this->questionModel->agregarPreguntaReportada($idPreguntaReportada);
    }
}