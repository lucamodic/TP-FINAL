<?php

class QuestionModel{

    private $database;
    private $pregunta_facil;
    private $pregunta_dificil;
    private $pregunta_default;
    public function __construct($database) {
        $this->database = $database;
        $this->pregunta_default = "SELECT * FROM pregunta WHERE agregada = 0 AND preg_default = 1";
        $this->pregunta_dificil = "SELECT * FROM pregunta WHERE agregada = 0 AND veces_respondida_bien * 100 / veces_respondida < 30 AND preg_default = 0";
        $this->pregunta_facil= "SELECT * FROM pregunta WHERE agregada=0 AND veces_respondida_bien * 100 / veces_respondida >= 30 AND preg_default = 0";
    }

    public function getRandomQuestion($nivelUsuario) {
        if($nivelUsuario != 1000){
            return $this->getPreguntaByDif($nivelUsuario);
        }
        return $this->loopWhilePreguntas($this->pregunta_default);
    }

    public function loopWhilePreguntas($sqlQuery){
        $x = true;
        while($x){
            $resultado = $this->database->query($sqlQuery);
            $random = rand(0, sizeof($resultado)-1);
            $pregunta = $resultado[$random];
            $x = $this->estaRespondida($pregunta['id'], $_SESSION['usuario']);
        }
        return $pregunta;
    }

    public function getPreguntaByDif($nivelUsuario){
        if($nivelUsuario >= 50){
            return $this->getPreguntaDificil();
        }
        return $this->getPreguntaFacil();
    }

    public function getPreguntaDificil(){
        if($this->getTamañoPreguntasDificiles() > 0){
            if($this->getTamañoPreguntasDificiles() != $this->getPreguntasDificilesQueRespondioElUsuario($_SESSION['usuario'])){
                return $this->loopWhilePreguntas($this->pregunta_dificil);
            }
        }
        return $this->getPreguntaFacil();
    }

    public function getPreguntaFacil(){
        if($this->getTamañoPreguntasFaciles() > 0) {
            if($this->getTamañoPreguntasFaciles() != $this->getPreguntasFacilesQueRespondioElUsuario($_SESSION['usuario'])){
                return $this->loopWhilePreguntas($this->pregunta_facil);
            }
        }
        return $this->getPreguntaDificil();
    }

    public function getPreguntasDificilesQueRespondioElUsuario($usuario){
        $sql = "SELECT * FROM preguntas_usadas pu
         JOIN pregunta p ON pu.pregunta_id = p.id
         WHERE pu.username LIKE '$usuario'
         AND p.agregada = 0 AND p.veces_respondida_bien * 100 / p.veces_respondida < 30
         AND p.preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function getTamañoPreguntasDificiles(){
        $sql = "SELECT * FROM pregunta p 
         WHERE  p.agregada = 0 AND p.veces_respondida_bien * 100 / p.veces_respondida < 30
         AND p.preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function getPreguntasFacilesQueRespondioElUsuario($usuario){
        $sql = "SELECT * FROM preguntas_usadas pu
         JOIN pregunta p ON pu.pregunta_id = p.id
         WHERE pu.username LIKE '$usuario'
         AND p.agregada=0 AND p.veces_respondida_bien * 100 / p.veces_respondida >= 30
         AND p.preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function getTamañoPreguntasFaciles(){
        $sql = "SELECT * FROM pregunta p 
         WHERE  p.agregada=0 AND p.veces_respondida_bien * 100 / p.veces_respondida >= 30
         AND p.preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function agregarPreguntaARespondida($pregunta, $usuario){
        $sql = "INSERT INTO preguntas_usadas (username, pregunta_id) values ('$usuario', '$pregunta')";
        $this->database->execute($sql);
        $sql = "UPDATE pregunta SET veces_respondida = veces_respondida + 1 WHERE id = '$pregunta'";
        $this->database->execute($sql);
    }

    public function estaRespondida($pregunta, $usuario){
        $sql = "SELECT * FROM preguntas_usadas 
         WHERE username LIKE '$usuario'
         AND pregunta_id LIKE '$pregunta'";
        return sizeof($this->database->query($sql)) > 0;
    }

    public function getPreguntasRespondidasQueNoSeanDefault($usuario){
        $sql = "SELECT * FROM preguntas_usadas pu
         JOIN pregunta p ON pu.pregunta_id = p.id
         WHERE pu.username LIKE '$usuario'
         AND p.agregada = false
         AND p.preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function agregarPreguntaReportada($idPreguntaReportada){
            $sql = "UPDATE pregunta SET reportada = 1 WHERE id = '$idPreguntaReportada'";
            $this->database->execute($sql);
    }

    public function getPreguntasReportadas(){
        $sql = "SELECT * FROM pregunta WHERE reportada = 1";
        return $this->database->query($sql);
    }

    public function getCategoriasQueEstenAgregadas(){
        $sql="SELECT nombre FROM categoria WHERE agregada = 0";
        return $this->database->query($sql);
    }

    public function getCategoria($idCategoria){
        $sql = "SELECT * FROM categoria WHERE id = '$idCategoria'";
        return $this->database->query($sql);
    }

    public function getCategoriasNuevas(){
        $sql = "SELECT * FROM categoria WHERE agregada = 1";
        return $this->database->query($sql);
    }

    public function setPreguntasAgregadas($data){
        $enunciado=$data["enunciado"];
        $categoriaNombre=$data["categoria"];
        $sql2="SELECT id FROM categoria WHERE nombre='$categoriaNombre'";
        $categoria= intval($this->database->query($sql2));
        $respuesta1 = $data["respuesta1"];
        $respuesta2 = $data["respuesta2"];
        $respuesta3 = $data["respuesta3"];
        $respuesta4 = $data["respuesta4"];
        $esEditor = $data["esEditor"];
        if(!$esEditor){
        $sql = "INSERT INTO pregunta(id_categoria, enunciado, reportada, agregada, veces_respondida, preguntaCreadaPorUsuario)
        values('$categoria', '$enunciado', false, true, 1, true);";}
        else{
        $sql = "INSERT INTO pregunta(id_categoria, enunciado, reportada, agregada, veces_respondida, preguntaCreadaPorUsuario)
        values('$categoria', '$enunciado', false, false, 1, true);";
        }
        $this->database->execute($sql);

        $sql7="SELECT * FROM pregunta WHERE enunciado='$enunciado'";
        $id= $this->database->query($sql7)[0]['id'];

        $sql3 = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta1', '$id', false);";
        $this->database->execute($sql3);
        $sql4 = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta2', '$id', false);";
        $this->database->execute($sql4);
        $sql5 = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta3', '$id', false);";
        $this->database->execute($sql5);
        $sql6 = "INSERT INTO respuesta(texto, id_pregunta, es_correcta)values('$respuesta4', '$id', true);";
        $this->database->execute($sql6);

    }

    public function getPreguntasNuevas(){
        $sql = "SELECT * FROM pregunta WHERE agregada = 1";
        return $this->database->query($sql);

    }

    public function getRespuestasNuevas(){
        $preguntas = $this->getPreguntasNuevas();
        $preguntasConRespuestas = [];

        foreach ($preguntas as $pregunta){
            $idPregunta = $pregunta['id'];
            $sql = "SELECT * FROM respuesta WHERE id_pregunta = '$idPregunta'";
            $respuestas = $this->database->query($sql);

            $preguntaConRespuestas = [
                'pregunta' => $pregunta,
                'respuestas' => $respuestas,
            ];

            $preguntasConRespuestas[] = $preguntaConRespuestas;
        }
        return $preguntasConRespuestas;
    }

    public function getPreguntasEditor(){
        $sql = "SELECT * FROM pregunta WHERE agregada = 0";
        $preguntas = $this->database->query($sql);
        $preguntasConRespuestas = [];

        foreach ($preguntas as $pregunta){
            $idPregunta = $pregunta['id'];
            $sql = "SELECT * FROM respuesta WHERE id_pregunta = '$idPregunta'";
            $respuestas = $this->database->query($sql);

            $preguntaConRespuestas = [
                'pregunta' => $pregunta,
                'respuestas' => $respuestas,
                'categorias'=> $this->getCategoria($pregunta['id_categoria'])
            ];

            $preguntasConRespuestas[] = $preguntaConRespuestas;
        }
        return $preguntasConRespuestas;
    }

    public function reestablecerReportada($id){
        $sql = "UPDATE pregunta SET reportada = 0 WHERE id = '$id'";
        $this->database->execute($sql);
    }

    public function aceptarNueva($id){
        $sql = "UPDATE pregunta SET agregada = 0 WHERE id = '$id'";
        $this->database->execute($sql);
    }

    public function setNuevaCategoria($categoria, $color, $es_editor){
        if($es_editor){
            $sql = "INSERT INTO categoria(nombre,agregada,color)
            values('$categoria',0,'$color')";
        } else{
            $sql = "INSERT INTO categoria(nombre,agregada,color)
            values('$categoria',1,'$color')";
        }
        $this->database->execute($sql);
    }


    public function eliminarNuevaCategoria($id){
        $sql="DELETE FROM categoria WHERE id='$id'";
        $this->database->execute($sql);
    }

    public function aceptarNuevaCategoria($id){
        $sql = "UPDATE categoria SET agregada = 0 WHERE id = '$id'";
        $this->database->execute($sql);
    }

    public function sumarAcertada($id){
        $sql = "UPDATE pregunta 
                SET veces_respondida_bien = veces_respondida_bien + 1 
                WHERE id LIKE '$id'";
        $this->database->execute($sql);
    }

    public function getPreguntasDisponibles(){
        $sql = "SELECT * FROM pregunta WHERE agregada = false AND preg_default = false";
        return sizeof($this->database->query($sql));
    }

    public function borrarTodasLasPreguntas($username){
        $sql = "DELETE FROM preguntas_usadas WHERE username LIKE '$username'";
        return $this->database->execute($sql);
    }

    public function verificarSiRespondioTodasLasPreguntas($user){
        if($this->getPreguntasDisponibles() == $this->getPreguntasRespondidasQueNoSeanDefault($user['username'])){
            $this->borrarTodasLasPreguntas($user['username']);
        }
    }

    public function mostrarTodasLasPreguntas(){
        $sql="SELECT * FROM pregunta";
        return $this->database->query($sql);
    }

    public function mostrarTodasLasRespuestas(){
        $sql="SELECT * FROM respuesta";
        return $this->database->query($sql);
    }

    public function eliminar($id){
        $sql2="DELETE FROM respuesta WHERE id_pregunta='$id'";
        $this->database->execute($sql2);
        $sql3="DELETE FROM preguntas_usadas WHERE pregunta_id='$id'";
        $this->database->execute($sql3);
        $sql="DELETE FROM pregunta WHERE id = '$id'";
        $this->database->execute($sql);
    }

    public function buscarPreguntaParaEditar($id){
        $sql = "SELECT * FROM pregunta WHERE id='$id'";
        return $this->database->query($sql);
    }

    public function editarPregunta($data){
        $id = $data['id'];
        $enunciado = $data['enunciado'];
        if($enunciado){
            $sql = "UPDATE pregunta SET enunciado='$enunciado' WHERE id = '$id'";
            $this->database->execute($sql);
        }
    }



    public function getColor($idCategoria){
        $sql = "SELECT * FROM categoria WHERE id = '$idCategoria'";
        return $this->database->query($sql);
    }
}