<?php
require_once "app/core/DB.php";

class Clase {
    public static function all() {
        $pdo = DB::pdo();
        $sql = "SELECT c.*, i.nombre AS instructor 
                FROM clases c
                LEFT JOIN instructores i ON c.instructor_id = i.id
                ORDER BY c.id DESC";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("INSERT INTO clases(nombre, categoria, instructor_id, dias, hora_inicio, hora_fin, estado)
                            VALUES(:nombre, :categoria, :instructor_id, :dias, :hora_inicio, :hora_fin, :estado)");
        $ok = $stmt->execute([
            "nombre"        => $data["nombre"],
            "categoria"     => $data["categoria"],
            "instructor_id" => $data["instructor_id"],
            "dias"          => $data["dias"],
            "hora_inicio"   => $data["hora_inicio"],
            "hora_fin"      => $data["hora_fin"],
            "estado"        => $data["estado"]
        ]);

        if (!$ok) {
            return false;
        }

        // 🔹 Devolver ID de la clase recién creada
        return (int)$pdo->lastInsertId();
    }

    public static function update($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE clases SET 
            nombre=:n, categoria=:cat, instructor_id=:i, dias=:d, 
            hora_inicio=:hi, hora_fin=:hf, estado=:e WHERE id=:id");
        return $stmt->execute([
            "id" => $data["id"],
            "n" => $data["nombre"],
            "cat" => $data["categoria"],
            "i" => $data["instructor_id"],
            "d" => $data["dias"],
            "hi" => $data["hora_inicio"],
            "hf" => $data["hora_fin"],
            "e" => $data["estado"]
        ]);
    }

    public static function delete($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM clases WHERE id=:id");
        return $stmt->execute(["id"=>$id]);
    }

    public static function find($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT * FROM clases WHERE id=:id LIMIT 1");
        $stmt->execute(["id"=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
