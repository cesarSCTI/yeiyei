<?php
require_once "app/core/Controller.php";
require_once "app/models/User.php";

class UserController extends Controller {
    public function index() {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "Administrador") {
            header("Location: index.php?route=auth/login");
            exit;
        }

        $users = User::all();
        $this->view("users/index", ["users"=>$users]);
    }

    public function store() {   
        $ok = User::create($_POST);
        echo json_encode(["ok"=>$ok]);      
    }

    public function update() {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "Administrador") {
            die("Acceso denegado");
        }

        $ok = User::update($_POST);
        echo json_encode(["ok"=>$ok]);
    }

    public function delete() {
        $ok = User::delete($_POST["id"]);
        echo json_encode(["ok"=>$ok]);
    }

    public function find() {
        $data = User::find($_POST["id"]);
        echo json_encode($data);
    }
}
    