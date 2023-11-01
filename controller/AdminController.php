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
        $this->adminModel->crearGraficoPaises();

        $this->renderer->render('admin');
    }

    public function mostrarCantidad(){
        $partida = $this->adminModel->contarCantidadDe('partida');
        $user = $this->adminModel->contarCantidadDe('user');
        $preguntasEnElJuego = $this->adminModel->contarCantidadDe('pregunta');
        $preguntasCreadas = $this->adminModel->cantidadDePreguntasCreadas();
        $data = [
          "cantidadPartidas" => $partida['0'],
          "cantidadUsuarios" => $user,
          "cantidadPreguntasEnElJuego" => $preguntasEnElJuego,
          "cantidadPreguntasCreadas" => $preguntasCreadas
        ];

        $this->renderer->render('mostrarDatos', $data);
    }
    public function mostrarGraficoPaises(){
        $this->adminModel->crearGraficoPaises();
        $this->renderer->render('grafico');
    }
}