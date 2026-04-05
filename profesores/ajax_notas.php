<?php
// ajax_notas.php (versión robusta — reemplazar)
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// debug en archivo
file_put_contents(__DIR__ . "/debug_notas.txt", "[".date('Y-m-d H:i:s')."] POST:\n" . print_r($_POST, true) . "\n", FILE_APPEND);

$action = $_POST['action'] ?? '';
if ($action !== 'guardar_notas') {
    echo json_encode(["status" => "error", "msg" => "Acción no válida"]);
    exit;
}

require_once("../crud/config.php");

// valores recibidos
$id_profesor = intval($_POST['id_profesor'] ?? 0);
$id_curso    = intval($_POST['id_curso'] ?? 0);

// id_materia puede venir o no. Si viene y es >0 lo usamos, si no lo almacenamos como NULL
$id_materia_raw = $_POST['id_materia'] ?? null;
$id_materia = (is_numeric($id_materia_raw) && intval($id_materia_raw) > 0) ? intval($id_materia_raw) : null;

$data = json_decode($_POST['data'] ?? '[]', true);
if (!is_array($data)) $data = [];

if (!$id_profesor || !$id_curso || empty($data)) {
    // no forzamos id_materia; permitimos que sea null — pero profesor/curso/data son obligatorios.
    echo json_encode(["status" => "error", "msg" => "Datos inválidos (falta profesor, curso o data)", "debug_post" => $_POST]);
    exit;
}

$conexion->begin_transaction();
try {
    // Recorremos filas y construimos queries seguras con escaping.
    foreach ($data as $fila) {
        $id_alumno = intval($fila['id_alumno'] ?? 0);
        if (!$id_alumno) continue;

        // informes
        $inf1 = isset($fila['informe1']) && $fila['informe1'] !== '' ? $conexion->real_escape_string($fila['informe1']) : null;
        $inf2 = isset($fila['informe2']) && $fila['informe2'] !== '' ? $conexion->real_escape_string($fila['informe2']) : null;

        // notas (decimal) o NULL
        $nota1 = (isset($fila['nota1']) && is_numeric($fila['nota1'])) ? floatval($fila['nota1']) : null;
        $nota2 = (isset($fila['nota2']) && is_numeric($fila['nota2'])) ? floatval($fila['nota2']) : null;

        $promedio = (is_numeric($nota1) && is_numeric($nota2)) ? round(($nota1 + $nota2) / 2, 2) : null;

        // Preparar valores SQL: cadenas con comillas o NULL, números sin comillas o NULL
        $inf1_sql = $inf1 === null ? "NULL" : "'{$inf1}'";
        $inf2_sql = $inf2 === null ? "NULL" : "'{$inf2}'";
        $nota1_sql = $nota1 === null ? "NULL" : $nota1;
        $nota2_sql = $nota2 === null ? "NULL" : $nota2;
        $prom_sql  = $promedio === null ? "NULL" : $promedio;

        // id_materia en SQL: si $id_materia es null -> NULL; else el valor
        $id_materia_sql = $id_materia === null ? "NULL" : intval($id_materia);

        // Insert / update: usamos INSERT ... ON DUPLICATE KEY UPDATE
        // IMPORTANTE: tu tabla necesita una key única que identifique (id_alumno,id_profesor,id_curso,id_materia)
        // si no la tenés, deberías crear un índice UNIQUE para evitar duplicados. Si no existe UNIQUE, el ON DUPLICATE no funcionará como esperás.
        $sql = "INSERT INTO notas (id_alumno, id_profesor, id_curso, id_materia, informe1, nota1, informe2, nota2, promedio_final)
                VALUES ({$id_alumno}, {$id_profesor}, {$id_curso}, {$id_materia_sql}, {$inf1_sql}, {$nota1_sql}, {$inf2_sql}, {$nota2_sql}, {$prom_sql})
                ON DUPLICATE KEY UPDATE
                  informe1 = COALESCE(informe1, VALUES(informe1)),
                  nota1    = COALESCE(nota1, VALUES(nota1)),
                  informe2 = COALESCE(informe2, VALUES(informe2)),
                  nota2    = COALESCE(nota2, VALUES(nota2)),
                  promedio_final = COALESCE(promedio_final, VALUES(promedio_final))";

        if (!$conexion->query($sql)) {
            throw new Exception("SQL error: " . $conexion->error . " -- QUERY: " . $sql);
        }
    }

    $conexion->commit();
    echo json_encode(["status" => "ok", "msg" => "Notas guardadas correctamente"]);
    exit;

} catch (Exception $e) {
    $conexion->rollback();
    // log
    file_put_contents(__DIR__ . "/debug_notas.txt", "[".date('Y-m-d H:i:s')."] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(["status" => "error", "msg" => "Error guardando notas: " . $e->getMessage()]);
    exit;
}
