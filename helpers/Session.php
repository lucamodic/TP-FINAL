<?php
function conectar($module, $router){

    if ($module != "user" && !isset($_SESSION["usuario"])) {
        $router->route('user', 'login');
        exit();
    }
    else if ($module == "admin" && !isset($_SESSION['admin'])){
        $router->route('home', 'mostrar');
        exit();
    }
    else if ($module != "admin" && isset($_SESSION['admin'])){
        $router->route('admin', 'admin');
        exit();
    }

}