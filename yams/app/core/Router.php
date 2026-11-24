<?php
class Router {
    public function dispatch() {
        $route = $_GET["route"] ?? "auth/login";
        $parts = explode("/", $route);

        $controllerName = ucfirst($parts[0]) . "Controller";
        $method = $parts[1] ?? "index";

        $file = "app/controllers/$controllerName.php";
        if (file_exists($file)) {
            require_once $file;
            $controller = new $controllerName();
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                echo "Método $method no encontrado";
            }
        } else {
            echo "Controlador $controllerName no encontrado";
        }
    }
}