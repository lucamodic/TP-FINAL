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

    public function checkAnswer(){
        if(!isset($_POST['URL'])){
            $this->renderer->render('home', $this->getDataCheater());
            exit();
        }
        $respuesta = $_POST['bool'];
        $pregunta = $_POST['id_pregunta'];
        if($respuesta){
            $this->userModel->sumarPuntos($_SESSION['usuario']);
            $this->partidaModel->sumarPuntos($_POST['id_partida']);

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
        }
        $_SESSION['tiempo'] = time();
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

    public function calcularTiempoQueQueda(){
        $tiempo = time() - $_SESSION['tiempo'];
        if($tiempo >= 11){
            $this->end();
        }
        $response = array("tiempo" => $tiempo);
        echo json_encode($response);
    }

    public function checkQuestion($usuario){
        $this->checkCount($usuario);
        $boolean = true;
        while($boolean){
            $pregunta = $this->questionModel->getRandomQuestion();
            $boolean = $this->questionModel->isAnswered($pregunta['id'], $usuario);
        }
        $this->questionModel->addQuestionToAnswered($pregunta['id'], $usuario);
        return $pregunta;
    }

    public function checkCount($usuario){
        $resultadoUsuario = $this->questionModel->getQuestionsAsked($usuario);
        $resultadoPreguntas = $this->questionModel->getQuestions();
        if($resultadoUsuario !== null && $resultadoPreguntas == $resultadoUsuario){
            $this->questionModel->deleteUserAnsweredQuestions($usuario);
        }
    }

    public function getDataGame(){
        $usuario = $this->userModel->getUserFromDatabaseWhereUsernameExists($_SESSION['usuario']);
        $pregunta = $this->checkQuestion($usuario['username']);
        $respuestas = $this->respuestaModel->getRespuestas($pregunta['id']);
        $partida = $_POST['id_partida'];
        return $data = [
            'partida' => $partida,
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
            $pregunta = $this->questionModel->agarrarUltimaPregunta($username)[0];
            $partida = $this->partidaModel->getPartidaByUsername($username);
        }else {
            $pregunta = $this->checkQuestion($username);
            $partida = $this->partidaModel->crearPartida($username);
        }
        $respuestas = $this->respuestaModel->getRespuestas($pregunta['id']);
        return $data = [
            'partida' => $partida[0]['id'],
            'pregunta' => $pregunta,
            'respuestas' => $respuestas,
            'username' => $username,
            'image' => $usuario['image']
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