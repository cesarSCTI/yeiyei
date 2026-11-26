<?php
require_once "app/core/Controller.php";
require_once "app/models/Cliente.php";
require_once "app/models/Instructor.php";
require_once "app/models/Pago.php";
require_once "app/models/Egreso.php";
require_once "app/models/Especialidad.php";
require_once "app/models/User.php";
require_once "app/models/Clase.php";
require_once "app/models/ClaseAlumno.php";
require_once "app/models/TipoClase.php";
require_once "app/models/IngresoExtra.php";
require_once "app/core/DB.php";

class DashboardController extends Controller {
    public function index() {
        if (!isset($_SESSION["user"])) {
            header("Location: index.php?route=auth/login");
            exit;
        }

        // El estado de cada cliente depende del estado de su último pago:
        // se pone al día antes de calcular tablas y recuadros (ajuste 21).
        Pago::sincronizarEstados();

        // Datos accesibles a todos
        $clientes       = Cliente::all();
        $instructores   = Instructor::all();
        $pagos          = Pago::all();
        $especialidades = Especialidad::all();

        // Totales
        $ingresosMes = Pago::sumThisMonth();
        $egresosMes  = Egreso::sumThisMonth();

        // Solo si es Administrador
        $egresos = [];
        $users   = [];
        if ($_SESSION["user"]["role"] === "Administrador") {
            $egresos = Egreso::all();
            $users   = User::all(); 
        }

        $clases = Clase::all();
        $clase_alumnos = ClaseAlumno::all();      
        
        $tipos = TipoClase::all();


        $this->view("dashboard/index", [
            "clientes"       => $clientes,
            "instructores"   => $instructores,
            "pagos"          => $pagos,
            "ingresosMes"    => $ingresosMes,
            "egresosMes"     => $egresosMes,
            "egresos"        => $egresos,
            "especialidades" => $especialidades,
            "users"          => $users,
            "clases" => $clases,
            "clase_alumnos" => $clase_alumnos,
            "tipos" => $tipos
        ]);
    }

    public function resumen() {
        if (!isset($_SESSION["user"])) {
            $this->json(["ok" => false, "msg" => "No autorizado"]);
        }

        // Estados por cliente (último pago), misma lógica que la tabla de Clientes
        $estados = Cliente::countByEstadoPago();

        // Ingresos del mes en curso: pagos + ingresos extra
        $ingresosMes = (float)Pago::sumThisMonth() + IngresoExtra::sumThisMonth();

        $this->json([
            "ok"          => true,
            "clientes"    => Cliente::countActivos(),
            "vencidos"    => $estados["Vencido"],
            "porVencer"   => $estados["Por Vencer"],
            "ingresosMes" => round($ingresosMes, 2)
        ]);
    }

    // Ingresos de los últimos 12 meses (pagos + ingresos extra), para la gráfica del Dashboard
    // Ruta: index.php?route=dashboard/ingresosMensuales
    public function ingresosMensuales() {
        if (!isset($_SESSION["user"])) {
            $this->json(["ok" => false, "msg" => "No autorizado"]);
        }

        $db = DB::pdo();
        $desde = (new DateTime("first day of this month"))->modify("-11 months")->format("Y-m-d");

        $stmt = $db->prepare("
            SELECT DATE_FORMAT(fecha_pago, '%Y-%m') AS mes, SUM(monto) AS total
            FROM pagos
            WHERE fecha_pago >= :desde
            GROUP BY mes
        ");
        $stmt->execute(["desde" => $desde]);
        $pagos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $stmt = $db->prepare("
            SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, SUM(costo) AS total
            FROM ingresos_extras
            WHERE fecha >= :desde
            GROUP BY mes
        ");
        $stmt->execute(["desde" => $desde]);
        $extras = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Siempre 12 meses, aunque alguno no tenga movimientos
        $meses = [];
        $mes = new DateTime($desde);
        for ($i = 0; $i < 12; $i++) {
            $clave = $mes->format("Y-m");
            $meses[] = [
                "mes"    => $clave,
                "pagos"  => round((float)($pagos[$clave] ?? 0), 2),
                "extras" => round((float)($extras[$clave] ?? 0), 2),
            ];
            $mes->modify("+1 month");
        }

        $this->json(["ok" => true, "meses" => $meses]);
    }
}
