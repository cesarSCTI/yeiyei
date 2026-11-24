<?php
// ========= SESIÓN PERSISTENTE =========
ini_set('session.gc_maxlifetime', 60 * 60 * 8); // 8 horas
session_set_cookie_params([
    'lifetime' => 60 * 60 * 8,  // 8 horas
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once "config/config.php";
require_once "app/core/Router.php";

$route = $_GET['route'] ?? '';

if (!isset($_SESSION['user'])) {
    // ✅ Permitir cualquier ruta que empiece con "auth/"
    if (substr($route, 0, 5) !== 'auth/') {
        header("Location: " . BASE_URL . "index.php?route=auth/login");
        exit;
    }
}

$router = new Router();
$router->dispatch();