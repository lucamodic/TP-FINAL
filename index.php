<?php
include_once('Configuration.php');
$configuration = new Configuration($_GET['module']);
$router = $configuration->getRouter();

$module = $_GET['module'] ?? 'home';
$method = $_GET['action'] ?? 'mostrar';

<<<<<<< HEAD

=======
if($module == "user" && isset($_SESSION["usuario"])){
    $router->route('home', 'mostrar');
    exit();
}
else if ($module != "user" && !isset($_SESSION["usuario"])){
    $router->route('user', 'login');
    exit();
}
>>>>>>> 516e1eed09cb7bc046e766fc8e98073316b8c910

$router->route($module, $method);
