<?php
require_once "app/core/Controller.php";
require_once "app/models/Pago.php";
require_once "app/models/Egreso.php";
require_once "app/models/IngresoExtra.php";
require_once "app/core/DB.php";

class FinanzasController extends Controller {

    /**
     * Resumen general (para las tarjetas de Ingresos/Egresos/Balance)
     * Ruta: index.php?route=finanzas/resumen
     */
    public function resumen() {
        if (!isset($_SESSION["user"])) {
            echo json_encode(["ok" => false, "msg" => "No autorizado"]);
            exit;
        }

        $db = DB::pdo();

        // Permite cambiar el periodo:
        //  - por defecto: MES (como estaba)
        //  - si scope=day: SOLO el día actual
        // Nota: para roles Operador y Administrador, FORZAMOS el scope=day
        //       para evitar que reciban datos mensuales por error.
        $scope = $_GET["scope"] ?? "month";

        $role = $_SESSION['user']['role'] ?? '';

        // Ambos roles trabajan con datos del día
        if (in_array($role, ['Operador', 'Administrador'])) {
            $scope = 'day';
        }

        if ($scope === "day") {
            // Totales SOLO del día actual
            $wherePagos   = "DATE(fecha_pago) = CURDATE()";
            $whereExtras  = "DATE(fecha) = CURDATE()";
            $whereEgresos = "DATE(fecha) = CURDATE()";
        } else {
            // Totales acumulados del MES en curso (comportamiento original)
            $wherePagos   = "MONTH(fecha_pago) = MONTH(CURDATE()) AND YEAR(fecha_pago) = YEAR(CURDATE())";
            $whereExtras  = "MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
            $whereEgresos = "MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
        }

        // ========== INGRESOS (PAGOS) POR FORMA DE PAGO ==========
        $ingresosEfectivo = $db->query("
            SELECT IFNULL(SUM(monto), 0) AS total
            FROM pagos
            WHERE forma_pago = 'Efectivo'
            AND {$wherePagos}
        ")->fetch()['total'];

        $ingresosTarjeta = $db->query("
            SELECT IFNULL(SUM(monto), 0) AS total
            FROM pagos
            WHERE forma_pago = 'Tarjeta'
            AND {$wherePagos}
        ")->fetch()['total'];

        $ingresosTransfer = $db->query("
            SELECT IFNULL(SUM(monto), 0) AS total
            FROM pagos
            WHERE forma_pago = 'Transferencia'
            AND {$wherePagos}
        ")->fetch()['total'];

        // ========== EGRESOS TOTALES ==========
        $egresos = $db->query("
            SELECT IFNULL(SUM(monto), 0) AS total
            FROM egresos
            WHERE {$whereEgresos}
        ")->fetch()['total'];

        // ========== INGRESOS EXTRA POR FORMA DE PAGO ==========
        $extrasEfectivo = $db->query("
            SELECT IFNULL(SUM(costo), 0) AS total
            FROM ingresos_extras
            WHERE forma_pago = 'Efectivo'
            AND {$whereExtras}
        ")->fetch()['total'];

        $extrasTarjeta = $db->query("
            SELECT IFNULL(SUM(costo), 0) AS total
            FROM ingresos_extras
            WHERE forma_pago = 'Tarjeta'
            AND {$whereExtras}
        ")->fetch()['total'];

        $extrasTransfer = $db->query("
            SELECT IFNULL(SUM(costo), 0) AS total
            FROM ingresos_extras
            WHERE forma_pago = 'Transferencia'
            AND {$whereExtras}
        ")->fetch()['total'];

        $extrasTotal    = $extrasEfectivo + $extrasTarjeta + $extrasTransfer;

        // ========== TOTALES GLOBALES ==========
        $ingresosTotales = $ingresosEfectivo + $ingresosTarjeta + $ingresosTransfer + $extrasTotal;
        $balance         = $ingresosTotales - $egresos;

        echo json_encode([
            "ok"                  => true,

            // Ingresos "normales" (pagos)
            "efectivo"            => (float)$ingresosEfectivo,
            "tarjeta"             => (float)$ingresosTarjeta,
            "transferencia"       => (float)$ingresosTransfer,

            // Ingresos EXTRA por forma de pago
            "extra_efectivo"      => (float)$extrasEfectivo,
            "extra_tarjeta"       => (float)$extrasTarjeta,
            "extra_transferencia" => (float)$extrasTransfer,
            "extra_total"         => (float)$extrasTotal,

            // Totales globales
            "ingresos"            => (float)$ingresosTotales,
            "egresos"             => (float)$egresos,
            "balance"             => (float)$balance
        ]);
    }

    /**
     * Lista de ingresos extras
     * Ruta: index.php?route=finanzas/extrasList
     */
    public function extrasList() {
        if (!isset($_SESSION["user"])) {
            echo json_encode(["ok" => false, "msg" => "No autorizado"]);
            exit;
        }

        $lista = IngresoExtra::all();
        echo json_encode([
            "ok"   => true,
            "data" => $lista
        ]);
    }

    /**
     * Guardar un ingreso extra
     * Ruta: index.php?route=finanzas/extrasStore
     */
    public function extrasStore() {
        if (!isset($_SESSION["user"])) {
            echo json_encode(["ok" => false, "msg" => "No autorizado"]);
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            exit;
        }

        $evento     = trim($_POST["evento"]     ?? "");
        $fecha      = trim($_POST["fecha"]      ?? "");
        $costo      = trim($_POST["costo"]      ?? "");
        $formaPago  = trim($_POST["forma_pago"] ?? "");

        if ($evento === "" || $fecha === "" || $costo === "" || $formaPago === "") {
            echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
            exit;
        }

        $ok = IngresoExtra::create([
            "evento"     => $evento,
            "fecha"      => $fecha,
            "costo"      => $costo,
            "forma_pago" => $formaPago
        ]);

        echo json_encode(["ok" => (bool)$ok]);
    }

    /**
     * Editar un ingreso extra, solo Administrador (ajuste 19)
     * Ruta: index.php?route=finanzas/extrasUpdate
     */
    public function extrasUpdate() {
        $this->soloAdministrador(); // ajustes 14 y 15

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->json(["ok" => false, "msg" => "Método no permitido"]);
        }

        $id        = (int)($_POST["id"] ?? 0);
        $evento    = trim($_POST["evento"]     ?? "");
        $fecha     = trim($_POST["fecha"]      ?? "");
        $costo     = trim($_POST["costo"]      ?? "");
        $formaPago = trim($_POST["forma_pago"] ?? "");

        $fechaValida = DateTime::createFromFormat("Y-m-d", $fecha);
        if ($id <= 0 || $evento === "" || !$fechaValida || $fechaValida->format("Y-m-d") !== $fecha
            || !is_numeric($costo) || (float)$costo < 0
            || !in_array($formaPago, ["Efectivo", "Tarjeta", "Transferencia"], true)) {
            $this->json(["ok" => false, "msg" => "Revisa los datos del ingreso extra"]);
        }

        if (!IngresoExtra::find($id)) {
            $this->json(["ok" => false, "msg" => "El ingreso extra ya no existe"]);
        }

        $ok = IngresoExtra::update([
            "id"         => $id,
            "evento"     => $evento,
            "fecha"      => $fecha,
            "costo"      => $costo,
            "forma_pago" => $formaPago
        ]);

        $this->json(["ok" => (bool)$ok, "ingreso" => $ok ? IngresoExtra::find($id) : null]);
    }

    /**
     * Eliminar un ingreso extra
     * Ruta: index.php?route=finanzas/extrasDelete
     */
    public function extrasDelete() {
        $this->soloAdministrador(); // ajustes 14 y 15
        if (!isset($_SESSION["user"])) {
            echo json_encode(["ok" => false, "msg" => "No autorizado"]);
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            exit;
        }

        $id = $_POST["id"] ?? null;
        if (!$id) {
            echo json_encode(["ok" => false, "msg" => "ID requerido"]);
            exit;
        }

        $ok = IngresoExtra::delete($id);

        echo json_encode(["ok" => (bool)$ok]);
    }

    /**
     * Ingresos (pagos + ingresos extras) por día del mes en curso.
     * Ruta: index.php?route=finanzas/ingresosDiarios
     */
    public function ingresosDiarios() {
        if (!isset($_SESSION["user"])) {
            echo json_encode(["ok" => false, "msg" => "No autorizado"]);
            exit;
        }

        $db = DB::pdo();

        // Pagos por día del mes actual
        $stmtPagos = $db->query("
            SELECT DATE(fecha_pago) AS fecha, IFNULL(SUM(monto), 0) AS total
            FROM pagos
            WHERE YEAR(fecha_pago) = YEAR(CURDATE())
              AND MONTH(fecha_pago) = MONTH(CURDATE())
            GROUP BY DATE(fecha_pago)
        ");
        $map = [];
        while ($row = $stmtPagos->fetch(PDO::FETCH_ASSOC)) {
            $map[$row['fecha']] = (float)$row['total'];
        }

        // Ingresos extras por día del mes actual
        $stmtExtras = $db->query("
            SELECT fecha, IFNULL(SUM(costo), 0) AS total
            FROM ingresos_extras
            WHERE YEAR(fecha) = YEAR(CURDATE())
              AND MONTH(fecha) = MONTH(CURDATE())
            GROUP BY fecha
        ");
        while ($row = $stmtExtras->fetch(PDO::FETCH_ASSOC)) {
            $f     = $row['fecha'];
            $total = (float)$row['total'];
            if (isset($map[$f])) {
                $map[$f] += $total;
            } else {
                $map[$f] = $total;
            }
        }

        // Construir arreglo de todos los días del mes actual
        $year        = (int)date("Y");
        $month       = (int)date("m");
        $daysInMonth = (int)date("t");

        $dias = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $fecha = sprintf("%04d-%02d-%02d", $year, $month, $d);
            $dias[] = [
                "fecha" => $fecha,
                "total" => isset($map[$fecha]) ? (float)$map[$fecha] : 0.0
            ];
        }

        echo json_encode([
            "ok"    => true,
            "year"  => $year,
            "month" => $month,
            "dias"  => $dias
        ]);
    }

}
