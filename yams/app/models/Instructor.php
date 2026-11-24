<?php
require_once "app/core/DB.php";

class Instructor {
    public static function all() {
        $pdo = DB::pdo();
        $stmt = $pdo->query("SELECT * FROM instructores WHERE estado = 'Activo' ORDER BY id DESC;");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function create($nombre, $telefono, $especialidad, $estado) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("INSERT INTO instructores (nombre, telefono, especialidad, fecha_registro, estado)
                               VALUES (:n, :t, :e, NOW(), :s)");
        return $stmt->execute([
            "n" => $nombre,
            "t" => $telefono,
            "e" => $especialidad,
            "s" => $estado
        ]);
    }

    public static function find($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT * FROM instructores WHERE id = :id LIMIT 1");
        $stmt->execute(["id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $nombre, $telefono, $especialidad, $estado) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE instructores 
                               SET nombre=:n, telefono=:t, especialidad=:e, estado=:s 
                               WHERE id=:id");
        return $stmt->execute([
            "id" => $id,
            "n"  => $nombre,
            "t"  => $telefono,
            "e"  => $especialidad,
            "s"  => $estado
        ]);
    }

    public static function delete($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE instructores SET estado = 'Suspendido' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}