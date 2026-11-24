<?php
require_once "app/core/DB.php";

class User {
    public static function all() {
        $pdo = DB::pdo();
        return $pdo->query("SELECT id, username, name, role, created_at FROM users ORDER BY id DESC")
                   ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("INSERT INTO users (username, password, name, role) 
                               VALUES (:u, :p, :n, :r)");
        return $stmt->execute([
            "u" => $data["username"],
            "p" => password_hash($data["password"], PASSWORD_BCRYPT),
            "n" => $data["name"],
            "r" => $data["role"]
        ]);
    }

    public static function update($data) {
        $pdo = DB::pdo();
        $fields = ["username = :u", "name = :n", "role = :r"];
        $params = [
            "u" => $data["username"],
            "n" => $data["name"],
            "r" => $data["role"],
            "id" => $data["id"]
        ];
        if (!empty($data["password"])) {
            $fields[] = "password = :p";
            $params["p"] = password_hash($data["password"], PASSWORD_BCRYPT);
        }
        $stmt = $pdo->prepare("UPDATE users SET ".implode(",",$fields)." WHERE id=:id");
        return $stmt->execute($params);
    }

    public static function delete($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=:id");
        return $stmt->execute(["id"=>$id]);
    }

    public static function find($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT id, username, name, role FROM users WHERE id=:id");
        $stmt->execute(["id"=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByUsername($username) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
        $stmt->execute(["u" => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }    
}
