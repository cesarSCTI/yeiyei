<?php
require_once "app/core/DB.php";

class Especialidad {
    public static function all() {
        $pdo = DB::pdo();
        $stmt = $pdo->query("SELECT * FROM especialidades ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("INSERT INTO especialidades (nombre, descripcion) VALUES (:n, :d)");
        return $stmt->execute([
            'n' => $data['nombre'],
            'd' => $data['descripcion']
        ]);
    }

    public static function update($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE especialidades SET nombre=:n, descripcion=:d WHERE id=:id");
        return $stmt->execute([
            'n' => $data['nombre'],
            'd' => $data['descripcion'],
            'id' => $data['id']
        ]);
    }

    public static function find($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT * FROM especialidades WHERE id=:id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM especialidades WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }
}