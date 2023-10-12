<?php
include_once('Configuration.php');
$configuration = new Configuration($_GET['module']);
$router = $configuration->getRouter();

$module = $_GET['module'] ?? 'home';
$method = $_GET['action'] ?? 'mostrar';



$router->route($module, $method);
