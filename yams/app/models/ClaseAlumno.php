<?php
require_once "app/core/DB.php";

class ClaseAlumno {
    public static function allByClase($clase_id) {
        $pdo = DB::pdo();
        $sql = "SELECT ca.*, c.nombre AS cliente 
                FROM clase_alumnos ca
                LEFT JOIN clientes c ON ca.cliente_id = c.id
                WHERE ca.clase_id = :cid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["cid"=>$clase_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function all() {
        $pdo = DB::pdo();
        $sql = "SELECT ca.*, 
                    cl.nombre AS cliente, 
                    cs.nombre AS clase
                FROM clase_alumnos ca
                LEFT JOIN clientes cl ON ca.cliente_id = cl.id
                LEFT JOIN clases cs ON ca.clase_id = cs.id
                ORDER BY cs.nombre, cl.nombre";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function assign($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("INSERT INTO clase_alumnos 
            (clase_id, cliente_id, estado) 
            VALUES (:c, :cl, 'Activo')");
        return $stmt->execute([
            "c" => $data["clase_id"],
            "cl" => $data["cliente_id"]
        ]);
    }

    public static function update($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE clase_alumnos 
            SET estado=:e WHERE id=:id");
        return $stmt->execute([
            "id"=>$data["id"],
            "e"=>$data["estado"]
        ]);
    }

    public static function delete($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM clase_alumnos WHERE id=:id");
        return $stmt->execute(["id"=>$id]);
    }
}
