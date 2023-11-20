<?php
require_once ('third-party/jpgraph-4.4.2/src/jpgraph.php');
require_once ('third-party/jpgraph-4.4.2/src/jpgraph_bar.php');
require_once ('third-party/jpgraph-4.4.2/src/jpgraph_pie.php');
require_once ('third-party/jpgraph-4.4.2/src/jpgraph_line.php');
require('third-party/fpdf/fpdf.php');

class AdminModel{
    private $database;

    public function __construct($database){$this->database = $database;}

    public function crearGraficoPaises($tiempo, $paraCambiarRuta){

        $data = $this->agarrarPaises($tiempo);

        $graph = new PieGraph(400, 300);

        $plot = new PiePlot(array_values($data));

        $coloresAleatorios = $this->generarColoresAleatorios(count($data));

        $plot->SetSliceColors($coloresAleatorios);

        $plot->SetLegends(array_map(function ($pais, $contador) {
            return "$pais [$contador]";
        }, array_keys($data), array_values($data)));

        $graph->Add($plot);

        $graph->title->Set("PAISES (". $tiempo . " Dias)");

        // Guardo la imagen
        $graph->Stroke('public/images/graficos/graficoPaises'.$paraCambiarRuta.'.png');
    }

    public function crearGraficoPorSexo($tiempo){
        $this->crearGrafico($this->cantidadUsuariosPorSexo($tiempo), "Grafico Por Sexos", "graficoPorSexo");
    }

    public function crearGraficoPorEdad($tiempo){
        $this->crearGrafico($this->getGruposDeEdad($tiempo), "Grafico Por Edad", "graficoPorEdad");
    }

    public function crearGraficoPorPreguntasRespondidasBienPorUsuario($tiempo){
        $this->crearGrafico($this->getPorcentajePreguntasRespondidas($tiempo),
            "Preguntas respondidas bien por usuario", "graficoPreguntasBien");
    }

    public function crearGrafico($data, $titulo, $nombreDelArchivoSinExtension){
        $graph = new PieGraph(400, 300);

        $plot = new PiePlot(array_values($data));

        $coloresAleatorios = $this->generarColoresAleatorios(count($data));

        $plot->SetSliceColors($coloresAleatorios);

        $plot->SetLegends(array_map(function ($dato, $contador) {
            return "$dato [$contador]";
        }, array_keys($data), array_values($data)));

        $graph->Add($plot);

        $graph->title->Set($titulo);

        $graph->Stroke('public/images/graficos/' . $nombreDelArchivoSinExtension . '.png');
    }

    function generarColoresAleatorios($cantidad) {
        $colores = [];
        for ($i = 0; $i < $cantidad; $i++) {
            $color = "#" . substr(md5(mt_rand()), 0, 6);
            $colores[] = $color;
        }
        return $colores;
    }

    public function agarrarPaises($tiempo){
        $apiKey = 'AIzaSyB7e9X-iFFD8Sc6YZIY8DPShMfmWAbaC90';
        $usuarios = $this->agarrarTodosLosUsuarios($tiempo);
        $paises = [];
        foreach ($usuarios as $usuario) {
            $latitude = $usuario['latitud'];
            $longitude = $usuario['longitud'];
            $apiUrl = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=$apiKey";
            $respuesta = file_get_contents($apiUrl);
            if ($respuesta !== false) {
                $data = json_decode($respuesta);
                if ($data && isset($data->status) && $data->status === 'OK') {
                    $addressComponents = $data->results[0]->address_components;
                    foreach ($addressComponents as $component) {
                        if (in_array('country', $component->types)) {
                            $pais = $component->long_name;
                            if (isset($paises[$pais])) {
                                $paises[$pais]++;
                            } else {
                                $paises[$pais] = 1;
                            }
                            break;
                        }
                    }
                }
            }
        }
        $data = array();
        foreach ($paises as $pais => $contador) {
            $data[$pais] = $contador;
        }
        return $data;
    }

    public function agarrarTodosLosUsuarios($tiempo){
        $sql = 'SELECT * FROM user WHERE fecha_de_creacion >= DATE_SUB(CURDATE(), INTERVAL ' . $tiempo . ' DAY) AND esAdmin = 0 AND esEditor = 0';
        return $this->database->query($sql);
    }

    public function cantidadUsuariosPorSexo($tiempo){
        $sqlMasculinos = "SELECT COUNT(*) FROM user WHERE sex = 'masculino' 
                            AND fecha_de_creacion <= DATE_SUB(CURDATE(), INTERVAL ' . $tiempo . ' DAY)";
        $masculinos = $this->database->fetchColumn($sqlMasculinos);
        $sqlFemenino = "SELECT COUNT(*) FROM user WHERE sex = 'femenino'
                            AND fecha_de_creacion <= DATE_SUB(CURDATE(), INTERVAL ' . $tiempo . ' DAY)";
        $femeninos = $this->database->fetchColumn($sqlFemenino);
        $sqlNoEspecifica = "SELECT COUNT(*) FROM user WHERE sex = 'x' 
                            AND fecha_de_creacion <= DATE_SUB(CURDATE(), INTERVAL ' . $tiempo . ' DAY)";
        $noEspecifica = $this->database->fetchColumn($sqlNoEspecifica);
        $data = [
            'masculinos' => $masculinos,
            'femeninos' => $femeninos,
            'x' => $noEspecifica
        ];
        return $data;
    }

    public function getGruposDeEdad($tiempo){
        $jubilados = 0;
        $menores = 0;
        $medio = 0;
        $sql = 'SELECT spawn FROM user 
                    WHERE fecha_de_creacion >= DATE_SUB(CURDATE(), INTERVAL ' . $tiempo . ' DAY) AND esAdmin = 0 AND esEditor = 0';
        $nacimientos = $this->database->query($sql);
        foreach ($nacimientos as $nacimiento) {
            $fecha = date('Y', strtotime($nacimiento['spawn']));
            if ($fecha < 1963) {
                $jubilados++;
            } else if ($fecha > 2014) {
                $menores++;
            } else {
                $medio++;
            }
        }
        $data = [
            "jubilados" => $jubilados,
            "menores" => $menores,
            "medio" => $medio
        ];
        return $data;
    }

    public function contarCantidadDe($tabla){
        $sql = "SELECT COUNT(*) FROM ".$tabla;
        return $this->database->fetchColumn($sql);
    }

    public function cantidadDePreguntasCreadas(){
        $sql = "SELECT COUNT(*) FROM pregunta where preguntaCreadaPorUsuario = TRUE";
        return $this->database->fetchColumn($sql);
    }

    public function cantidadDeUsuariosNuevos(){
        $sql = "SELECT COUNT(*) FROM user WHERE fecha_de_creacion <= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        return $this->database->fetchColumn($sql);
    }

    public function getPorcentajePreguntasRespondidas($tiempo){
        $sql='SELECT SUM(veces_respondida) as total_veces_respondida FROM pregunta 
                WHERE fecha_de_creacion >= DATE_SUB(CURDATE(), INTERVAL ' . $tiempo . ' DAY)';
        $vecesRespondidasTotal = $this->database->fetchColumn($sql);
        $sql2 = 'SELECT SUM(veces_respondida_bien) as veces_respondida_bien FROM pregunta
                    WHERE fecha_de_creacion >= DATE_SUB(CURDATE(), INTERVAL ' . $tiempo . ' DAY)';
        $vecesRespondidasBien = $this->database->fetchColumn($sql2);
        $data = [
            "Respondidas Bien" => $vecesRespondidasBien,
            "Respondidas Total" => $vecesRespondidasTotal
        ];
        return $data;
    }

    public function generarReporte(){
        $partida = $this->contarCantidadDe('partida');
        $user = $this->contarCantidadDe('user');
        $preguntasEnElJuego = $this->contarCantidadDe('pregunta');
        $preguntasCreadas = $this->cantidadDePreguntasCreadas();
        $jugadoresNuevos = $this->cantidadDeUsuariosNuevos();

        $pdf = new FPDF();
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 16);
        $titulo = "Reporte de Questionario";
        $pdf->Cell(0, 10, $titulo, 0, 1, 'C');
        $text = '||Cantidad de partidas jugadas: ' . $partida . '
        ||Cantidad de usuarios: ' . $user . '
        ||Cantidad de preguntas en el juego: ' . $preguntasEnElJuego . '
        ||Cantidad de preguntas creadas: ' . $preguntasCreadas . '
        ||Cantidad de jugadores nuevos: ' . $jugadoresNuevos .'';
        $lines = explode("||", $text);
        foreach ($lines as $line) {
            $pdf->Cell(0, 10, $line, 0, 1, 'L');
        }
        $pdf->Output('public/images/pdf/example.pdf', 'D');

    }

}