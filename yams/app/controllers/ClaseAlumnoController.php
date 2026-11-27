<?php
require_once "app/core/Controller.php";
require_once "app/models/ClaseAlumno.php";
require_once "app/models/Cliente.php";

class ClaseAlumnoController extends Controller {
    public function index() {
        $clase_id = $_GET["clase_id"] ?? null;
        if (!$clase_id) { echo "Clase no especificada"; return; }

        $alumnos = ClaseAlumno::allByClase($clase_id);
        $clientes = Cliente::all();

        $this->view("clases/alumnos", [
            "alumnos"=>$alumnos,
            "clientes"=>$clientes,
            "clase_id"=>$clase_id
        ]);
    }

    public function getByClase() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $clase_id = $_POST["clase_id"];
            $alumnos = ClaseAlumno::allByClase($clase_id);
            echo json_encode($alumnos);
        }
    }

    
    public function assign() {
        $ok = ClaseAlumno::assign($_POST);
        echo json_encode(["ok"=>$ok]);
    }

    public function update() {
        $this->soloAdministrador(); // ajustes 14 y 15
        $ok = ClaseAlumno::update($_POST);
        echo json_encode(["ok"=>$ok]);
    }

    public function delete() {
        $this->soloAdministrador(); // ajustes 14 y 15
        $ok = ClaseAlumno::delete($_POST["id"]);
        echo json_encode(["ok"=>$ok]);
    }
}
