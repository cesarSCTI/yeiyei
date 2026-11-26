<?php
require_once __DIR__ . "/Permisos.php";

class Controller {
    public function view($view, $data = []) {
        extract($data);
        require "app/views/layout/header.php";
        require "app/views/$view.php";
        require "app/views/layout/footer.php";
    }

    public function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Corta la petición si el usuario no puede modificar ni eliminar registros
     * (ajustes 14 y 15). Responde en JSON porque estas rutas se llaman por fetch.
     */
    protected function soloAdministrador(): void {
        if (yams_puede_modificar()) {
            return;
        }
        http_response_code(403);
        $this->json([
            "ok"  => false,
            "msg" => "Solo el Administrador puede modificar o eliminar registros."
        ]);
    }
}
