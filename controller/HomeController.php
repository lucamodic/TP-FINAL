<?php

class HomeController
{
    private $renderer;

    public function __construct($renderer) {
        $this->renderer = $renderer;
    }

    public function mostrar(){
        $this->renderer->render('home');
    }


}