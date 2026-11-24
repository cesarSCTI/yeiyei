<?php
require_once "app/core/DB.php";

class TipoClase {
    public static function all() {
        $pdo = DB::pdo();
        return $pdo->query("SELECT * FROM tipoclases ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("INSERT INTO tipoclases (nombre, descripcion) VALUES (:n, :d)");
        return $stmt->execute([
            "n" => $data["nombre"],
            "d" => $data["descripcion"],
        ]);
    }

    public static function update($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE tipoclases SET nombre=:n, descripcion=:d WHERE id=:id");
        return $stmt->execute([
            "id" => $data["id"],
            "n" => $data["nombre"],
            "d" => $data["descripcion"]
        ]);
    }

    public static function delete($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM tipoclases WHERE id=:id");
        return $stmt->execute(["id" => $id]);
    }

    public static function find($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT * FROM tipoclases WHERE id=:id");
        $stmt->execute(["id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
