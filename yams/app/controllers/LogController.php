<?php
require_once "app/core/Controller.php";
require_once "app/models/Log.php";

class LogController extends Controller {
    public function index() {
        if (!isset($_SESSION["user"])) {
            header("Location: index.php?route=auth/login");
            exit;
        }

        $logs = Log::all();
        $this->view("logs/index", ["logs" => $logs]);
    }
}
