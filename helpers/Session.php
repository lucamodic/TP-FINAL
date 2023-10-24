<?php
function conectar($module, $router){

     if ($module != "user" && !isset($_SESSION["usuario"])){
        $router->route('user', 'login');
        exit();
    }


}