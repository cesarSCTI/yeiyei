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
require_once "app/core/DB.php";

class DashboardController extends Controller {
    public function index() {
        if (!isset($_SESSION["user"])) {
            header("Location: index.php?route=auth/login");
            exit;
        }

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
        header('Content-Type: application/json');

        $db = DB::pdo();

        $clientes = $db->query("SELECT COUNT(*) AS total FROM clientes WHERE estado = 'Activo'")->fetch()['total'];
        $vencidos = $db->query("SELECT COUNT(*) AS total FROM pagos WHERE estado = 'Vencido'")->fetch()['total'];
        $porVencer = $db->query("SELECT COUNT(*) AS total FROM pagos WHERE estado = 'Por Vencer'")->fetch()['total'];
        $clases = $db->query("SELECT COUNT(*) AS total FROM clases WHERE estado = 'Activa'")->fetch()['total'];

        echo json_encode([
            "ok" => true,
            "clientes" => (int)$clientes,
            "vencidos" => (int)$vencidos,
            "porVencer" => (int)$porVencer,
            "clases" => (int)$clases
        ]);
    }    
}