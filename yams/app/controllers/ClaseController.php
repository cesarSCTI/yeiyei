<?php
require_once "app/core/Controller.php";
require_once "app/models/Clase.php";
require_once "app/models/Instructor.php";
require_once "app/models/ClaseHorario.php"; // 🔹 Agregado


class ClaseController extends Controller {
    public function index() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?route=auth/login");
            exit;
        }

        $clases = Clase::all();
        $instructores = Instructor::all();

        $this->view("clases/index", [
            "clases"=>$clases,
            "instructores"=>$instructores
        ]);
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(["ok" => false]);
            return;
        }

        // 1) Crear la clase normal (tabla clases)
        $claseId = Clase::create($_POST);

        if ($claseId) {
            // 2) Guardar horarios por día (tabla clase_horarios)
            $horasInicio = $_POST["hora_inicio"] ?? [];
            $horasFin    = $_POST["hora_fin"] ?? [];

            ClaseHorario::saveForClase($claseId, $horasInicio, $horasFin);

            echo json_encode(["ok" => true]);
        } else {
            echo json_encode(["ok" => false]);
        }
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(["ok" => false]);
            return;
        }

        $ok = Clase::update($_POST);

        if ($ok && !empty($_POST["id"])) {
            $claseId     = (int)$_POST["id"];
            $horasInicio = $_POST["hora_inicio"] ?? [];
            $horasFin    = $_POST["hora_fin"] ?? [];

            // 🔹 Reemplaza los horarios de esa clase por los nuevos
            ClaseHorario::saveForClase($claseId, $horasInicio, $horasFin);
        }

        echo json_encode(["ok" => $ok]);
    }


    public function delete() {
        $ok = Clase::delete($_POST["id"]);
        echo json_encode(["ok"=>$ok]);
    }

    public function find() {
        $data = Clase::find($_POST["id"]);
        echo json_encode($data);
    }
}
