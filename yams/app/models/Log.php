<?php
require_once "app/core/DB.php";

class Log {
    public static function create($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO logs (user_id, accion, entidad, entidad_id, descripcion)
            VALUES (:u, :a, :e, :eid, :d)
        ");
        return $stmt->execute([
            "u"   => $data["user_id"],
            "a"   => $data["accion"],
            "e"   => $data["entidad"],
            "eid" => $data["entidad_id"],
            "d"   => $data["descripcion"]
        ]);
    }

    public static function all() {
        $pdo = DB::pdo();
        $stmt = $pdo->query("
            SELECT l.*, u.username, u.name 
            FROM logs l
            JOIN users u ON l.user_id = u.id
            ORDER BY l.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
