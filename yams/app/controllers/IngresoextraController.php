<?php
require_once "app/core/Controller.php";
require_once "app/core/DB.php";
require_once "app/models/IngresoExtra.php";

class IngresoextraController extends Controller {

    /** 
     * Pequeño helper para recalcular el resumen de finanzas 
     * considerando también los ingresos extras.
     */
    private function resumenFinanzasConExtras() {
        $pdo = DB::pdo();

        $ingresosEfectivo = $pdo->query("
            SELECT IFNULL(SUM(monto),0) AS total 
            FROM pagos 
            WHERE forma_pago = 'Efectivo'
        ")->fetch()["total"];

        $ingresosTarjeta = $pdo->query("
            SELECT IFNULL(SUM(monto),0) AS total 
            FROM pagos 
            WHERE forma_pago = 'Tarjeta'
        ")->fetch()["total"];

        $ingresosTransfer = $pdo->query("
            SELECT IFNULL(SUM(monto),0) AS total 
            FROM pagos 
            WHERE forma_pago = 'Transferencia'
        ")->fetch()["total"];

        $egresos = $pdo->query("
            SELECT IFNULL(SUM(monto),0) AS total 
            FROM egresos
        ")->fetch()["total"];

        $extras = $pdo->query("
            SELECT IFNULL(SUM(costo),0) AS total 
            FROM ingresos_extras
        ")->fetch()["total"];

        $ingresosTotales = $ingresosEfectivo + $ingresosTarjeta + $ingresosTransfer + $extras;
        $balance = $ingresosTotales - $egresos;

        return [
            "efectivo"      => (float)$ingresosEfectivo,
            "tarjeta"       => (float)$ingresosTarjeta,
            "transferencia" => (float)$ingresosTransfer,
            "extras"        => (float)$extras,
            "ingresos"      => (float)$ingresosTotales,
            "egresos"       => (float)$egresos,
            "balance"       => (float)$balance,
        ];
    }

    // Listado JSON para llenar la tabla
    public function list() {
        if (!isset($_SESSION["user"])) {
            echo json_encode([]);
            return;
        }
        $ingresos = IngresoExtra::all();
        echo json_encode($ingresos);
    }

    // Guardar nuevo ingreso extra
    public function store() {
        if (!isset($_SESSION["user"])) {
            echo json_encode(["ok" => false, "msg" => "No autorizado"]);
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            return;
        }

        $evento = trim($_POST["evento"] ?? "");
        $fecha  = $_POST["fecha"] ?? "";
        $costo  = $_POST["costo"] ?? "";

        if ($evento === "" || $fecha === "" || $costo === "") {
            echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
            return;
        }

        $ingreso = IngresoExtra::create([
            "evento" => $evento,
            "fecha"  => $fecha,
            "costo"  => $costo,
        ]);

        if (!$ingreso) {
            echo json_encode(["ok" => false, "msg" => "No se pudo guardar"]);
            return;
        }

        $finanzas = $this->resumenFinanzasConExtras();

        echo json_encode([
            "ok"       => true,
            "ingreso"  => $ingreso,
            "finanzas" => $finanzas
        ]);
    }

    // Eliminar ingreso extra
    public function delete() {
        if (!isset($_SESSION["user"])) {
            echo json_encode(["ok" => false, "msg" => "No autorizado"]);
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            return;
        }

        $id = (int)($_POST["id"] ?? 0);
        if ($id <= 0) {
            echo json_encode(["ok" => false, "msg" => "ID inválido"]);
            return;
        }

        $ok = IngresoExtra::delete($id);
        if (!$ok) {
            echo json_encode(["ok" => false, "msg" => "No se pudo eliminar"]);
            return;
        }

        $finanzas = $this->resumenFinanzasConExtras();

        echo json_encode([
            "ok"       => true,
            "finanzas" => $finanzas
        ]);
    }
}
