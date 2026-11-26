<?php
require_once "app/core/Controller.php";
require_once "app/models/Cliente.php";
require_once "app/models/TipoClase.php";
//require_once "app/models/Log.php";

class ClienteController extends Controller {

    // Mostrar listado de clientes
    public function index() {
        $clientes = Cliente::all();
        $tipos = TipoClase::all();

        // 👉 Cargar la vista correcta (clientes/index.php)
        $this->view("clientes/index", [
            "clientes" => $clientes,
            "tipos" => $tipos
        ]);
    }

    // Crear cliente nuevo
    public function store() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = $_POST["nombre"] ?? '';
            $telefono = $_POST["telefono"] ?? '';
            $tipo_clase = $_POST["tipo_clase"] ?? '';
            $fecha_registro = $_POST['fecha_registro'] ?? null;

            if ($fecha_registro) {
                $fecha_registro = str_replace('T', ' ', $fecha_registro);
                // Si viene solo fecha (YYYY-MM-DD), agrega hora
                if (strlen($fecha_registro) === 10) $fecha_registro .= ' 00:00:00';
                // Si viene sin segundos (YYYY-MM-DD HH:MM)
                if (strlen($fecha_registro) === 16) $fecha_registro .= ':00';
            }

            $estado = $_POST["estado"] ?? 'Activo';

            // Guarda cliente
            $ok = Cliente::create($nombre, $telefono, $tipo_clase, $fecha_registro, $estado);

            // Si se creó bien y vienen horarios, los guardamos
            if ($ok && !empty($_POST['horarios'])) {
                // Usamos el último ID insertado en esta conexión
                $pdo = DB::pdo();
                $idCliente = $pdo->lastInsertId();
                Cliente::mdlGuardarHorariosCliente($idCliente, $_POST['horarios']);
            }

            echo json_encode(["ok" => $ok]);
        }
    }


    // Actualizar cliente existente
    public function update() {
        $this->soloAdministrador(); // ajustes 14 y 15
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"] ?? 0;
            $nombre = $_POST["nombre"] ?? '';
            $telefono = $_POST["telefono"] ?? '';
            $tipo_clase = $_POST["tipo_clase"] ?? '';
            $fecha_registro = $_POST['fecha_registro'] ?? null;
            $estado = $_POST["estado"] ?? 'Activo';

            $ok = Cliente::update($id, $nombre, $telefono, $tipo_clase, $fecha_registro, $estado);

            // Si se actualizó bien y vienen horarios, los guardamos
            if ($ok && !empty($_POST['horarios'])) {
                Cliente::mdlGuardarHorariosCliente($id, $_POST['horarios']);
            }

            echo json_encode(["ok" => $ok]);
        }
    }



    // Eliminar cliente
    public function delete() {
        $this->soloAdministrador(); // ajustes 14 y 15
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"] ?? 0;
            $ok = Cliente::delete($id);
            echo json_encode(["ok" => $ok]);
        }
    }

    // Buscar un cliente por ID (para editar)
    public function find() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"] ?? 0;

            // Datos principales
            $cliente = Cliente::find($id);

            // 👉 Añadimos sus horarios
            $cliente['horarios'] = Cliente::mdlObtenerHorariosCliente($id);

            echo json_encode($cliente);
        }
    }


}