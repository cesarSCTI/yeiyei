<?php
require_once "app/core/Controller.php";
require_once "app/models/TipoClase.php";

class TipoclaseController extends Controller {
    public function index() {
        $tipos = TipoClase::all();
        $this->view("clientes/tipoclases", ["tipos" => $tipos]);
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header('Content-Type: application/json');
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            exit;
        }

        $ok = TipoClase::create($_POST);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["ok" => $ok]);
        exit;
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header('Content-Type: application/json');
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            exit;
        }

        $ok = TipoClase::update($_POST);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["ok" => $ok]);
        exit;
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            exit;
        }

        $id = $_POST["id"] ?? null;
        if (!$id) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "msg" => "ID requerido"]);
            exit;
        }

        $ok = TipoClase::delete($id);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["ok" => (bool)$ok]);
        exit;
    }

    public function find()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            exit;
        }

        $id = $_POST["id"] ?? null;
        if (!$id) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "msg" => "ID requerido"]);
            exit;
        }

        $tipo = TipoClase::find($id);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($tipo ?: []);
        exit;
    }
}
