<?php
include_once('Configuration.php');
$configuration = new Configuration();
$router = $configuration->getRouter();

$module = $_GET['module'] ?? 'home';
$method = $_GET['action'] ?? 'mostrar';

session_start();

if($module == "user" && isset($_SESSION["usuario"])){
    $router->route('home', 'mostrar');
    exit();
}
else if ($module != "user" && !isset($_SESSION["usuario"])){
    $router->route('user', 'login');
    exit();
}

$router->route($module, $method);
