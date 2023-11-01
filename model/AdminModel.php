<?php
require_once ('third-party/jpgraph-4.4.2/src/jpgraph.php');
require_once ('third-party/jpgraph-4.4.2/src/jpgraph_bar.php');
require_once ('third-party/jpgraph-4.4.2/src/jpgraph_pie.php');
require_once ('third-party/jpgraph-4.4.2/src/jpgraph_line.php');

class AdminModel{
    private $database;

    public function __construct($database){$this->database = $database;}

    public function crearPieGraphic (){
        //LE PASO LOS DATOS QUE VA A TENER ADENTRO
        $data = array(30, 45, 25);

        // Le pasa el tamaño
        $graph = new PieGraph(400, 300);

        // CREO ALGO
        $plot = new PiePlot($data);

        // PONGO LOS COLORES
        $plot->SetSliceColors(array('#FF5733', '#33FF57', '#5733FF'));

        // Pongo las categorias
        $plot->SetLegends(array('Slice 1', 'Slice 2', 'Slice 3'));

        //agrego al grafico
        $graph->Add($plot);

        // Titulo
        $graph->title->Set("Sample Pie Chart");

        // Guardo la imagen
        $graph->Stroke('public/images/graph.png');
    }

    public function crearGraficoPaises(){

        $data = $this->agarrarPaises();

        $graph = new PieGraph(400, 300);

        $plot = new PiePlot(array_values($data));

        $coloresAleatorios = $this->generarColoresAleatorios(count($data));

        $plot->SetSliceColors($coloresAleatorios);

        $plot->SetLegends(array_map(function ($pais, $contador) {
            return "$pais [$contador]";
        }, array_keys($data), array_values($data)));

        $graph->Add($plot);

        $graph->title->Set("PAISES");

        // Guardo la imagen
        $graph->Stroke('public/images/graficoPaises.png');
    }

    function generarColoresAleatorios($cantidad) {
        $colores = [];
        for ($i = 0; $i < $cantidad; $i++) {
            $color = "#" . substr(md5(mt_rand()), 0, 6);
            $colores[] = $color;
        }
        return $colores;
    }

    public function agarrarPaises(){
        $apiKey = 'AIzaSyB7e9X-iFFD8Sc6YZIY8DPShMfmWAbaC90';
        $usuarios = $this->agarrarTodosLosUsuarios();
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

    public function agarrarTodosLosUsuarios(){
        $sql = 'SELECT * FROM user';
        return $this->database->query($sql);
    }

    public function cantidadUsuariosPorSexo(){
        $sqlMasculinos = "SELECT COUNT (*) FROM user WHERE sex = 'masculino'";
        $masculinos = $this->database->query($sqlMasculinos);
        $sqlFemenino = "SELECT COUNT (*) FROM user WHERE sex = 'femenino'";
        $femeninos = $this->database->query($sqlFemenino);
        $sqlNoEspecifica = "SELECT COUNT (*) FROM user WHERE sex = 'x'";
        $noEspecifica = $this->database->query($sqlNoEspecifica);
        $data = [
            'masculinos' => $masculinos,
            'femeninos' => $femeninos,
            'x' => $noEspecifica
        ];
        return $data;
    }

    public function getGruposDeEdad()
    {
        $jubilados = 0;
        $menores = 0;
        $medio = 0;
        $sql = 'SELECT spawn FROM user';
        $nacimientos = $this->database->query($sql);
        foreach ($nacimientos as $nacimiento) {
            if ($nacimiento < 1963) {
                $jubilados++;
            } else if ($nacimiento > 2014) {
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

    public function cantidadDePreguntasCreadas(){
        $sql = "SELECT COUNT(*) FROM pregunta where preguntaCreadaPorUsuario = TRUE";
        return $this->database->query($sql);
    }

    public function contarCantidadDe($tabla){
        $sql = "SELECT COUNT(*) FROM ".$tabla;
        return $this->database->query($sql);
    }

    public function getPorcentajePreguntasRespondidas(){
        $vecesRespondidasCorrectamenteTotal = 0;
        $vecesRespondidasTotal = 0;
        $sql='SELECT veces_respondida FROM pregunta';
        $vecesRespondidas = $this->database->query($sql);
        foreach($vecesRespondidas as $vecesRespondida){
            $vecesRespondidasTotal+=$vecesRespondida;
        }
        $sql2 = 'SELECT veces_respondida_bien FROM pregunta';
        $vecesRespondidasBien = $this->database->query($sql2);
        foreach($vecesRespondidasBien as $vecesRespondidaBien){
            $vecesRespondidasCorrectamenteTotal += $vecesRespondidaBien;
        }
        return $vecesRespondidasCorrectamenteTotal*100/$vecesRespondidasTotal;
    }
}