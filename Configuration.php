<?php

session_start();

include_once('helpers/MySqlDatabase.php');
include_once("helpers/MustacheRender.php");
include_once('helpers/Router.php');
include_once('helpers/Logger.php');
include_once ("model/UserModel.php");
include_once ("model/QuestionModel.php");
include_once ("model/RespuestaModel.php");
include_once ("model/PartidaModel.php");
include_once ("model/AdminModel.php");
include_once('controller/HomeController.php');
include_once('controller/TiendaController.php');
include_once('controller/EditorController.php');
include_once('controller/UserController.php');
include_once('controller/AdminController.php');
include_once('controller/GameController.php');
include_once('third-party/mustache/src/Mustache/Autoloader.php');
include_once('helpers/Session.php');


class Configuration {
    private $configFile = 'config/config.ini';

    public function __construct($module) {
        conectar($module, $this->getRouter());
    }

    public function getUserController(){
        return new UserController(
            new UserModel($this->getDatabase()),
            $this->getRenderer()
        );
    }

    public function getAdminController(){
        return new AdminController(
            new  AdminModel($this->getDatabase()),
            $this->getRenderer()
        );
    }

    public function getTiendaController(){
        return new TiendaController(
            new UserModel($this->getDatabase()),
            $this->getRenderer()
        );
    }

    public function getGameController(){
        return new GameController(
            new QuestionModel($this->getDatabase()),
            new RespuestaModel($this->getDatabase()),
            new UserModel($this->getDatabase()),
            $this->getRenderer(),
            new PartidaModel($this->getDatabase())
        );
    }

    public function getHomeController() {
        return new HomeController(
            new UserModel($this->getDatabase()),
            $this->getRenderer(),
            new QuestionModel($this->getDatabase())
        );
    }

    public function getEditorController() {
        return new EditorController(
            new UserModel($this->getDatabase()),
            $this->getRenderer(),
            new QuestionModel($this->getDatabase()),
            new RespuestaModel($this->getDatabase())
        );
    }

    private function getArrayConfig() {
        return parse_ini_file($this->configFile);
    }

    private function getRenderer() {
        return new MustacheRender('view/partial');
    }

    public function getDatabase() {
        $config = $this->getArrayConfig();
        return new MySqlDatabase(
            $config['servername'],
            $config['username'],
            $config['password'],
            $config['database']);
    }

    public function getRouter() {
        return new Router(
            $this,
            "getHomeController",
            "mostrar");
    }

}