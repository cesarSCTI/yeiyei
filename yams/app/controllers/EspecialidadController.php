<?php
require_once "app/core/Controller.php";
require_once "app/models/Especialidad.php";

class EspecialidadController extends Controller {
    public function index() {
        $especialidades = Especialidad::all();
        $this->view("especialidades/index", ["especialidades" => $especialidades]);
    }

    public function store() {
        $ok = Especialidad::create($_POST);
        echo json_encode(["ok" => $ok]);
    }

    public function update() {
        $ok = Especialidad::update($_POST);
        echo json_encode(["ok" => $ok]);
    }

    public function delete() {
        $ok = Especialidad::delete($_POST['id']);
        echo json_encode(["ok" => $ok]);
    }

    public function find() {
        $data = Especialidad::find($_POST['id']);
        echo json_encode($data);
    }
}