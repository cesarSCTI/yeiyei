<?php
require_once "app/core/Controller.php";
require_once "app/models/Egreso.php";
require_once "app/models/Pago.php";
require_once "app/core/DB.php"; // Necesario para lastInsertId()

class EgresoController extends Controller {

    private function requireEgresoAccess(): void {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ["Administrador", "Operador"])) {
            header('Location: index.php?route=auth/login');
            exit;
        }
    }

    public function index() {
        $this->requireEgresoAccess();

        $egresos = Egreso::all();
        $this->view('egresos/index', ['egresos' => $egresos]);
    }

    public function store() {
        $this->requireEgresoAccess();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            exit;
        }

        // Recibir y limpiar datos
        $data = [
            "concepto"   => $_POST["concepto"] ?? "",
            "monto"      => $_POST["monto"] ?? 0,
            "forma_pago" => $_POST["forma_pago"] ?? "Efectivo",
            "fecha"      => $_POST["fecha"] ?? date("Y-m-d")
        ];

        $ok = Egreso::create($data);

        if ($ok) {
            $pdo = DB::pdo();
            $lastId = $pdo->lastInsertId();
            $egreso = Egreso::find($lastId);

            // Recalcular finanzas del mes
            $ingresos = Pago::sumThisMonth();
            $egresos  = Egreso::sumThisMonth();
            $balance  = $ingresos - $egresos;

            echo json_encode([
                "ok" => true,
                "egreso" => $egreso,
                "finanzas" => [
                    "ingresos" => (float)$ingresos,
                    "egresos"  => (float)$egresos,
                    "balance"  => (float)$balance
                ]
            ]);
        } else {
            echo json_encode(["ok" => false, "msg" => "No se pudo guardar"]);
        }
        exit;
    }

    public function update() {
        $this->soloAdministrador(); // ajustes 14 y 15
        $this->requireEgresoAccess();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            exit;
        }

        $data = [
            "id"         => $_POST["id"],
            "concepto"   => $_POST["concepto"] ?? "",
            "monto"      => $_POST["monto"] ?? 0,
            "forma_pago" => $_POST["forma_pago"] ?? "Efectivo",
            "fecha"      => $_POST["fecha"] ?? date("Y-m-d")
        ];

        $ok = Egreso::update($data);

        if ($ok) {
            $egreso = Egreso::find($_POST["id"]);
            echo json_encode(["ok" => true, "egreso" => $egreso]);
        } else {
            echo json_encode(["ok" => false]);
        }
        exit;
    }

    public function delete() {
        $this->soloAdministrador(); // ajustes 14 y 15
        $this->requireEgresoAccess();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            exit;
        }

        $ok = Egreso::delete($_POST["id"]);

        echo json_encode(["ok" => (bool)$ok]);
        exit;
    }

    public function find() {
        $this->requireEgresoAccess();
        
        if (!isset($_POST["id"])) {
            echo json_encode(["ok" => false, "msg" => "ID requerido"]);
            exit;
        }

        $data = Egreso::find($_POST["id"]);
        echo json_encode($data);
        exit;
    }
}