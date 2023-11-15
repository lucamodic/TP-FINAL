<?php

class AdminController
{
    private $adminModel;
    private $renderer;


    public function __construct($adminModel, $renderer) {
        $this->adminModel = $adminModel;
        $this->renderer = $renderer;
    }

    public function admin (){
        $this->renderer->render('admin');
    }

    public function mostrarCantidad(){
        $partida = $this->adminModel->contarCantidadDe('partida');
        $user = $this->adminModel->contarCantidadDe('user');
        $preguntasEnElJuego = $this->adminModel->contarCantidadDe('pregunta');
        $preguntasCreadas = $this->adminModel->cantidadDePreguntasCreadas();
        $jugadoresNuevos = $this->adminModel->cantidadDeUsuariosNuevos();
        $data = [
          "cantidadPartidas" => $partida,
          "cantidadUsuarios" => $user,
          "cantidadPreguntasEnElJuego" => $preguntasEnElJuego,
          "cantidadPreguntasCreadas" => $preguntasCreadas,
          "cantidadJugadoresNuevos" => $jugadoresNuevos
        ];
        $this->renderer->render('mostrarDatos', $data);
    }

    public function mostrarGraficoPaises(){
        $this->adminModel->crearGraficoPaises($_POST['tiempo'],"Temporal");
        $data = [
          'ruta' => '../public/images/graficos/graficoPaisesTemporal.png'
        ];
        $this->renderer->render('grafico',$data);
    }

    public function mostrarGraficoPreguntas(){
        $this->adminModel->crearGraficoPorPreguntasRespondidasBienPorUsuario($_POST['tiempo'],"Temporal");
        $data = [
            'ruta' => '../public/images/graficos/graficoPreguntasBien.png'
        ];
        $this->renderer->render('grafico',$data);
    }

    public function mostrarGraficoPorSexo(){
        $this->adminModel->crearGraficoPorSexo($_POST['tiempo']);
        $data = [
            'ruta' => '../public/images/graficos/graficoPorSexo.png'
        ];
        $this->renderer->render('grafico',$data);
    }

    public function mostrarGraficoPorEdad(){
        $this->adminModel->crearGraficoPorEdad($_POST['tiempo']);
        $data = [
            'ruta' => '../public/images/graficos/graficoPorEdad.png'
        ];
        $this->renderer->render('grafico',$data);
    }

    public function mostrarGraficoPreguntasRespondidas(){
        $this->adminModel->crearGraficoPorPreguntasRespondidasBienPorUsuario($_POST['tiempo']);
        $data = [
            'ruta' => '../public/images/graficos/graficoPreguntasBien.png'
        ];
        $this->renderer->render('grafico',$data);
    }

    public function mostrarReporte(){
        $this->adminModel->generarReporte();
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="public/images/pdf/example.pdf"');
        exit();
    }
    public function mostrarGraficos(){
        $this->renderer->render('graficos');
    }

    public function logout(){
        if (isset($_POST["Logout"])) {
            unset($_SESSION["usuario"]);
            unset($_SESSION["admin"]);
            $this->renderer->render('login');
            exit();
        }
    }
}