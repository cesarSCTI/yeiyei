<?php
require_once "app/core/Controller.php";
require_once "app/models/TipoClase.php";

class TipoclaseController extends Controller {

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            header('Content-Type: application/json; charset=utf-8');

            $data = [
                "nombre"      => $_POST["nombre"]      ?? "",
                "descripcion" => $_POST["descripcion"] ?? "",
                "estado"      => $_POST["estado"]      ?? "Activo",
            ];

            if (TipoClase::create($data)) {
                echo json_encode(["ok" => true]);
            } else {
                echo json_encode([
                    "ok"  => false,
                    "msg" => "Error al guardar tipo de clase"
                ]);
            }
        }
    }

    public function update() {
        $this->soloAdministrador(); // ajustes 14 y 15
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            header('Content-Type: application/json; charset=utf-8');

            $data = [
                "id"          => $_POST["id"]          ?? null,
                "nombre"      => $_POST["nombre"]      ?? "",
                "descripcion" => $_POST["descripcion"] ?? "",
                "estado"      => $_POST["estado"]      ?? "Activo",
            ];

            if (TipoClase::update($data)) {
                echo json_encode(["ok" => true]);
            } else {
                echo json_encode([
                    "ok"  => false,
                    "msg" => "Error al actualizar tipo de clase"
                ]);
            }
        }
    }

    public function find() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            header('Content-Type: application/json; charset=utf-8');

            $id = $_POST["id"] ?? null;
            if (!$id) {
                echo json_encode(["ok" => false, "msg" => "ID requerido"]);
                return;
            }

            $tipo = TipoClase::find($id);
            if ($tipo) {
                echo json_encode($tipo);
            } else {
                echo json_encode(["ok" => false, "msg" => "No encontrado"]);
            }
        }
    }

    public function delete() {
        $this->soloAdministrador(); // ajustes 14 y 15
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            header('Content-Type: application/json; charset=utf-8');

            $id = $_POST["id"] ?? null;
            if (!$id) {
                echo json_encode(["ok" => false, "msg" => "ID requerido"]);
                return;
            }

            if (TipoClase::delete($id)) {
                echo json_encode(["ok" => true]);
            } else {
                echo json_encode([
                    "ok"  => false,
                    "msg" => "Error al eliminar tipo de clase"
                ]);
            }
        }
    }
}
