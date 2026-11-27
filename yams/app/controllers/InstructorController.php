<?php
require_once "app/core/Controller.php";
require_once "app/models/Instructor.php";

class InstructorController extends Controller {

    public function index() {
        if (!isset($_SESSION["user"])) {
            header("Location: index.php?route=auth/login");
            exit;
        }

        // Traer todos los instructores
        $instructores = Instructor::all();

        // Pasar a la vista
        $this->view("instructores/index", ["instructores" => $instructores]);
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = $_POST["nombre"];
            $telefono = $_POST["telefono"];
            $especialidad = $_POST["especialidad"];
            $estado = $_POST["estado"];

            $ok = Instructor::create($nombre, $telefono, $especialidad, $estado);
            echo json_encode(["ok" => $ok]);
        }
    }

    public function update() {
        $this->soloAdministrador(); // ajustes 14 y 15
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"];
            $nombre = $_POST["nombre"];
            $telefono = $_POST["telefono"];
            $especialidad = $_POST["especialidad"];
            $estado = $_POST["estado"];

            $ok = Instructor::update($id, $nombre, $telefono, $especialidad, $estado);
            echo json_encode(["ok" => $ok]);
        }
    }

    public function delete() {
        $this->soloAdministrador(); // ajustes 14 y 15
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode([
                "ok" => false,
                "msg" => "Método no permitido"
            ]);
            exit;
        }

        $id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

        if ($id <= 0) {
            echo json_encode([
                "ok" => false,
                "msg" => "ID inválido"
            ]);
            exit;
        }

        try {
            $ok = Instructor::delete($id);

            echo json_encode([
                "ok" => $ok ? true : false,
                "msg" => $ok ? "Instructor desactivado correctamente" : "No se pudo desactivar el instructor"
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                "ok" => false,
                "msg" => $e->getMessage()
            ]);
        }

        exit;
    }

    public function find() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"];
            $instructor = Instructor::find($id);
            echo json_encode($instructor);
        }
    }
}
