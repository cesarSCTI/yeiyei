<?php
require_once "app/core/Controller.php";
require_once "app/models/Pago.php";
require_once "app/models/Egreso.php";

class PagoController extends Controller {
    public function index() {
        if (!isset($_SESSION['user'])) { 
            header('Location: index.php?route=auth/login'); 
            exit; 
        }
        $pagos = Pago::all();
        $this->view('pagos/index', ['pagos'=>$pagos]);
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $ok = Pago::create($_POST);

            // Obtenemos el resumen actualizado
            $ingresos = Pago::sumThisMonth();
            $egresos  = Egreso::sumThisMonth();
            $balance  = $ingresos - $egresos;

            $this->json([
                "ok" => $ok,
                "finanzas" => [
                    "ingresos" => $ingresos,
                    "egresos"  => $egresos,
                    "balance"  => $balance
                ]
            ]);
        }
    }

    public function historial() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $cliente_id = $_POST["cliente_id"];
            $pdo = DB::pdo();
            $stmt = $pdo->prepare("
                SELECT id, monto, fecha_pago, proximo_pago, estado
                FROM pagos 
                WHERE cliente_id = :cid
                ORDER BY fecha_pago DESC
            ");
            $stmt->execute(["cid" => $cliente_id]);
            $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->json($historial);
        }
    }

    public function find() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"];

            $pdo = DB::pdo();
            $sql = "SELECT 
                        p.id,
                        p.cliente_id,
                        c.nombre AS cliente,
                        p.monto,
                        p.forma_pago,
                        DATE(p.fecha_pago) AS fecha_pago,   -- solo la fecha (sin hora)
                        p.proximo_pago,
                        p.estado
                    FROM pagos p
                    LEFT JOIN clientes c ON p.cliente_id = c.id
                    WHERE p.id = :id
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(["id" => $id]);
            $pago = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->json($pago);
        }
    }

    public function update() {
        $data = $_POST;

        // Si es una actualización automática (estado/días)
        if (isset($data["accion"]) && $data["accion"] === "autoUpdateEstado") {
            $ok = Pago::update($data);
            return $this->json(["ok" => $ok]);
        }

        // Caso normal (edición manual)
        $ok = Pago::update($data);

        if ($ok) {
            $pdo = DB::pdo();
            $stmt = $pdo->prepare("SELECT p.*, c.nombre AS cliente, c.telefono 
                                FROM pagos p 
                                LEFT JOIN clientes c ON p.cliente_id=c.id 
                                WHERE p.id=:id");
            $stmt->execute(['id'=>$data['id']]);
            $pago = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->json(['ok'=>true, 'pago'=>$pago]);
        } else {
            $this->json(['ok'=>false]);
        }
    }
}