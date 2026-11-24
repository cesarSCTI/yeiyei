<?php
require_once "app/core/DB.php";

class ClaseHorario
{
    /**
     * Guarda los horarios por día de una clase.
     * Borra los anteriores y vuelve a insertar.
     *
     * @param int   $clase_id
     * @param array $horasInicio  $_POST["hora_inicio"][dia]
     * @param array $horasFin     $_POST["hora_fin"][dia]
     */
    public static function saveForClase($clase_id, $horasInicio, $horasFin)
    {
        $pdo = DB::pdo();

        // Borrar horarios previos de esa clase
        $del = $pdo->prepare("DELETE FROM clase_horarios WHERE clase_id = :id");
        $del->execute(["id" => $clase_id]);

        if (!is_array($horasInicio) || !is_array($horasFin)) {
            return;
        }

        $ins = $pdo->prepare("
            INSERT INTO clase_horarios (clase_id, dia, hora_inicio, hora_fin)
            VALUES (:clase_id, :dia, :hora_inicio, :hora_fin)
        ");

        foreach ($horasInicio as $dia => $inicio) {
            $inicio = trim($inicio ?? "");
            $fin    = trim($horasFin[$dia] ?? "");

            // Si algún campo viene vacío, lo ignoramos
            if ($inicio === "" || $fin === "") {
                continue;
            }

            $ins->execute([
                "clase_id"   => $clase_id,
                "dia"        => $dia,
                "hora_inicio"=> $inicio,
                "hora_fin"   => $fin
            ]);
        }
    }

    /**
     * Obtener horarios por día de una clase (útil después para edición o calendario)
     */
    public static function getByClase($clase_id)
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            SELECT dia, hora_inicio, hora_fin
            FROM clase_horarios
            WHERE clase_id = :id
            ORDER BY FIELD(dia, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')
        ");
        $stmt->execute(["id" => $clase_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
