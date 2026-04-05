
<?php
// === DEBUG TEMPORAL PARA VER ERRORES ===
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Guardar los errores en un archivo dentro de esta misma carpeta
$debugFile = __DIR__ . '/debug_notas_admin.txt';
file_put_contents($debugFile, "---- NUEVA EJECUCIÓN (" . date('Y-m-d H:i:s') . ") ----\n", FILE_APPEND);
file_put_contents($debugFile, "POST DATA:\n" . print_r($_POST, true) . "\n", FILE_APPEND);



header('Content-Type: application/json; charset=utf-8');
require_once("../../crud/config.php");

$action = $_POST['action'] ?? '';
if ($action !== 'guardar_notas') {
    echo json_encode(["status" => "error", "msg" => "Acción no válida"]);
    exit;
}

$id_curso = intval($_POST['id_curso'] ?? 0);
$id_materia = intval($_POST['id_materia'] ?? 0);
$id_profesor = intval($_POST['id_profesor'] ?? 0);

$data = json_decode($_POST['data'] ?? '[]', true);

if (!$id_curso || !$id_materia || empty($data)) {
    echo json_encode(["status" => "error", "msg" => "Datos incompletos"]);
    exit;
}

$conexion->begin_transaction();
try {
    foreach ($data as $fila) {
        $id_alumno = intval($fila['id_alumno'] ?? 0);
        if (!$id_alumno) continue;

        $inf1 = $conexion->real_escape_string($fila['informe1'] ?? '');
        $inf2 = $conexion->real_escape_string($fila['informe2'] ?? '');
        $nota1 = is_numeric($fila['nota1']) ? floatval($fila['nota1']) : null;
        $nota2 = is_numeric($fila['nota2']) ? floatval($fila['nota2']) : null;
        $promedio = (is_numeric($nota1) && is_numeric($nota2)) ? round(($nota1 + $nota2) / 2, 2) : null;

        $nota1_sql = $nota1 === null ? "NULL" : $nota1;
        $nota2_sql = $nota2 === null ? "NULL" : $nota2;
        $prom_sql  = $promedio === null ? "NULL" : $promedio;
        $inf1_sql = $inf1 === '' ? "NULL" : "'$inf1'";
        $inf2_sql = $inf2 === '' ? "NULL" : "'$inf2'";

        $sql = "INSERT INTO notas (id_alumno, id_profesor, id_curso, id_materia, informe1, nota1, informe2, nota2, promedio_final)
                VALUES ($id_alumno, $id_profesor, $id_curso, $id_materia, $inf1_sql, $nota1_sql, $inf2_sql, $nota2_sql, $prom_sql)
                ON DUPLICATE KEY UPDATE
                    informe1 = VALUES(informe1),
                    nota1 = VALUES(nota1),
                    informe2 = VALUES(informe2),
                    nota2 = VALUES(nota2),
                    promedio_final = VALUES(promedio_final)";
        
        if (!$conexion->query($sql)) {
            throw new Exception("Error SQL: " . $conexion->error);
        }
    }

    $conexion->commit();
    echo json_encode(["status" => "ok", "msg" => "Notas actualizadas correctamente."]);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
