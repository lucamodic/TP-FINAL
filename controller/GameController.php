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

    public function empezarJuego(){
        if($this->userModel->checkearVerificacion($_SESSION['usuario'])){
            $this->createSession();
            $this->renderer->render('game', $this->getDataParaCuandoEmpiezaLaPartida());
        }
        else {
            $this->renderer->render('home', $this->getDataNoEstaVerificado());
        }
    }

    public function perdiste(){
        $partida = $this->partidaModel->getPartidaPorUsername($_SESSION['usuario'])[0];
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $this->userModel->sumarPartidaRealizadas($_SESSION['usuario']);
        $this->partidaModel->finalizarJuego($partida['id']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        $data = [
            'puntajeUsuario'=> $usuario['puntaje'],
            'puntajeTotal' => $partida['puntaje'],
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'numeroRanking' => $numeroRanking
        ];
        $this->renderer->render('perdiste', $data);
    }

    public function verificarRespuesta(){
        if(!isset($_POST['URL'])){
            $this->renderer->render('home', $this->getDataNoAccedePorBoton());
            exit();
        }
        $pregunta = $_POST['id_pregunta'];
        $tiempo = - ($_SESSION['start_time'] - time());
        $this->checkearTrampita($tiempo, $pregunta);
        $respuesta = $_POST['bool'];
        if($respuesta && $tiempo <= 10){
            $this->respuestaAcertada($pregunta);
        }
        $this->userModel->sumarPartidaRealizadas($_SESSION['usuario']);
        $this->partidaModel->finalizarJuego($_POST['id_partida']);
        $this->renderer->render('perdiste', $this->getDataPerdio());
        exit();
    }

    public function checkearTrampita($tiempo, $pregunta){
        if($tiempo <= 10 && isset($_POST['trampita'])){
            $this->userModel->descontarTrampita($_SESSION['usuario']);
            $this->respuestaAcertada($pregunta);
        }
    }
    
    public function respuestaAcertada($pregunta){
        $this->userModel->sumarPuntos($_SESSION['usuario']);
        $this->partidaModel->sumarPuntos($_POST['id_partida']);
        $this->questionModel->sumarAcertada($pregunta);
        $this->createSession();
        $this->renderer->render('game', $this->getDataDelJuegoCuandoRespondeLaPregunta());
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
            $this->perdiste();
        }
        $response = array("tiempo" => $tiempo);
        echo json_encode($response);
    }

    public function agarrarPregunta($usuario){
        $dificultadUser = $this->userModel->agarrarDificultad($usuario);
        $this->verCuantasRespondio($usuario);
        $pregunta = $this->questionModel->getRandomQuestion($dificultadUser);
        $this->questionModel->agregarPreguntaARespondida($pregunta['id'], $usuario);
        $this->userModel->agregarRespondida($usuario);
        return $pregunta;
    }

    public function verCuantasRespondio($usuario){
        $user = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($usuario);
        $this->questionModel->verificarSiRespondioTodasLasPreguntas($user);
    }

    public function getDataParaCuandoEmpiezaLaPartida(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $username = $usuario['username'];
        if($this->partidaModel->verificarPartida($username)){
            $partida = $this->partidaModel->getPartidaPorUsername($username);
            $this->perdiste();
        }else {
            $pregunta = $this->agarrarPregunta($username);
            $partida = $this->partidaModel->crearPartida($username);
        }
        $respuestas = $this->respuestaModel->getRespuestas($pregunta['id']);
        $idCategoria = $pregunta['id_categoria'];
        $categoria = $this->questionModel->getColor($idCategoria);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        return $data = [
            'trampitas' => $usuario['trampitas'],
            'partida' => $partida[0]['id'],
            'pregunta' => $pregunta,
            'respuestas' => $respuestas,
            'username' => $username,
            'image' => $usuario['image'],
            'categoria' => $categoria,
            'numeroRanking' => $numeroRanking
        ];
    }

    public function getDataDelJuegoCuandoRespondeLaPregunta(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $pregunta = $this->agarrarPregunta($usuario['username']);
        $respuestas = $this->respuestaModel->getRespuestas($pregunta['id']);
        $partida = $_POST['id_partida'];
        $idCategoria = $pregunta['id_categoria'];
        $categoria = $this->questionModel->getColor($idCategoria);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        return $data = [
            'trampitas' => $usuario['trampitas'],
            'partida' => $partida,
            'pregunta' => $pregunta,
            'respuestas' => $respuestas,
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'categoria' => $categoria,
            'numeroRanking' => $numeroRanking
        ];
    }

    public function getDataNoEstaVerificado(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        return $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'verificar' => "Verifica tu mail antes de jugar!"
        ];
    }

    public function getDataPerdio(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        $partida = $this->partidaModel->getPartidaPorId($_POST['id_partida']);
        $numeroRanking = $this->userModel->getNumeroRanking($usuario['username']);
        return $data = [
            'puntajeUsuario'=> $usuario['puntaje'],
            'puntajeTotal' => $partida['puntaje'],
            'username' => $usuario['username'],
            'image' => $usuario['image'],
            'numeroRanking' => $numeroRanking
        ];
    }

    public function getDataNoAccedePorBoton(){
        $usuario = $this->userModel->agarrarUsuarioDeLaBaseDeDatosPorUsername($_SESSION['usuario']);
        return $data = [
            'username' => $usuario['username'],
            'image' => $usuario['image']
        ];
    }

    public function reportarPregunta(){
        $idPreguntaReportada = $_GET['id_pregunta'];
        $this->questionModel->agregarPreguntaReportada($idPreguntaReportada);
    }


}