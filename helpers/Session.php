<?php
function conectar($module, $router){

    if ($module != "user" && !isset($_SESSION["usuario"])) {
        $router->route('user', 'login');
        exit();
    }
    else if ($module == "admin" && !isset($_SEESION['admin'])){
        $router->route('home','home');
        exit();
    }
    else if ($module == "admin" && !isset($_SEESION['admin'])){
        $router->route('admin','admin');
        exit();
    }

}