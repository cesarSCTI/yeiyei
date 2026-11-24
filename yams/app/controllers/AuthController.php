<?php
require_once "app/core/Controller.php";
require_once "app/models/User.php";

class AuthController extends Controller {
    public function login() {
        if (isset($_SESSION['user'])) {
            header("Location: index.php?route=dashboard/index");
            exit;
        }
        $this->view("auth/login");
    }

    public function signin() {
        header('Content-Type: application/json'); // ✅ Forzar JSON en la respuesta

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"] ?? '');
            $password = trim($_POST["password"] ?? '');

            try {
                $user = User::findByUsername($username);

                if ($user && password_verify($password, $user["password"])) {
                    session_regenerate_id(true);

                    $_SESSION["user"] = [
                        "id"       => $user["id"],
                        "username" => $user["username"],
                        "name"     => $user["name"],
                        "role"     => $user["role"]
                    ];

                    echo json_encode([
                        "ok" => true,
                        "redirect" => "index.php?route=dashboard/index"
                    ]);
                } else {
                    echo json_encode([
                        "ok" => false,
                        "message" => "Usuario o contraseña incorrectos"
                    ]);
                }
            } catch (Throwable $e) {
                error_log("Error en AuthController::signin: " . $e->getMessage());
                http_response_code(500);

                echo json_encode([
                    "ok" => false,
                    "message" => "No fue posible conectar con la base de datos."
                ]);
            }
            exit;
        }

        // Si alguien entra a esta ruta por GET → error controlado
        echo json_encode(["ok" => false, "message" => "Método no permitido"]);
        exit;
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?route=auth/login");
        exit;
    }
}