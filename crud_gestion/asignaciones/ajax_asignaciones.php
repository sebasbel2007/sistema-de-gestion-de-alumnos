<?php
// ajax_asignaciones.php
header('Content-Type: application/json; charset=utf-8');

$conexion = new mysqli("localhost", "root", "", "sistema_escuela");
if ($conexion->connect_error) {
    echo json_encode(['status'=>'error','msg'=>'Error conexión DB: '.$conexion->connect_error]);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

/* CREAR ASIGNACIÓN */
if ($action === 'crear') {
    $id_curso = intval($_POST['id_curso'] ?? 0);
    $id_materia = intval($_POST['id_materia'] ?? 0);
    $id_profesor = intval($_POST['id_profesor'] ?? 0);

    if ($id_curso <= 0 || $id_materia <= 0 || $id_profesor <= 0) {
        echo json_encode(['status'=>'error','msg'=>'Faltan datos.']); exit;
    }

    // Opcional: evitar duplicados exactos (mismo curso+materia+profesor)
    $chk = $conexion->prepare("SELECT id FROM profesor_curso WHERE id_curso=? AND id_materia=? AND id_profesor=? LIMIT 1");
    $chk->bind_param('iii', $id_curso, $id_materia, $id_profesor);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
        echo json_encode(['status'=>'error','msg'=>'Asignación ya existe']); $chk->close(); exit;
    }
    $chk->close();

    $stmt = $conexion->prepare("INSERT INTO profesor_curso (id_profesor, id_curso, id_materia) VALUES (?, ?, ?)");
    $stmt->bind_param('iii', $id_profesor, $id_curso, $id_materia);
    if (!$stmt->execute()) {
        echo json_encode(['status'=>'error','msg'=>'Error al crear: '.$stmt->error]); $stmt->close(); exit;
    }
    $newId = $stmt->insert_id;
    $stmt->close();

    echo json_encode(['status'=>'ok','msg'=>'Asignación creada','id'=>$newId]);
    exit;
}

/* GUARDAR CAMBIOS (batch) - editar filas en tabla */
if ($action === 'guardar_cambios') {
    $data = json_decode($_POST['data'] ?? '[]', true);
    if (!is_array($data)) {
        echo json_encode(['status'=>'error','msg'=>'Datos inválidos']); exit;
    }

    $stmt = $conexion->prepare("UPDATE profesor_curso SET id_profesor=?, id_curso=?, id_materia=? WHERE id = ?");
    if (!$stmt) { echo json_encode(['status'=>'error','msg'=>'Prepare error: '.$conexion->error]); exit; }

    foreach ($data as $row) {
        $id = intval($row['id'] ?? 0);
        if ($id <= 0) continue;
        // esperamos keys: profesor, curso, materia o sus equivalentes id
        $id_prof = intval($row['profesor'] ?? $row['id_profesor'] ?? 0);
        $id_cur  = intval($row['curso'] ?? $row['id_curso'] ?? 0);
        $id_mat  = intval($row['materia'] ?? $row['id_materia'] ?? 0);

        if ($id_prof <= 0 || $id_cur <= 0 || $id_mat <= 0) {
            echo json_encode(['status'=>'error','msg'=>"Datos incompletos para ID $id"]); $stmt->close(); exit;
        }

        $stmt->bind_param('iiii', $id_prof, $id_cur, $id_mat, $id);
        if (!$stmt->execute()) {
            echo json_encode(['status'=>'error','msg'=>'Error ejecutando update: '.$stmt->error]); $stmt->close(); exit;
        }
    }
    $stmt->close();
    echo json_encode(['status'=>'ok','msg'=>'Cambios guardados']);
    exit;
}

/* BORRAR UNO */
if ($action === 'borrar_uno') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) { echo json_encode(['status'=>'error','msg'=>'ID inválido']); exit; }
    $stmt = $conexion->prepare("DELETE FROM profesor_curso WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status'=>'ok','msg'=>'Asignación borrada']);
    exit;
}

/* BORRAR VARIOS */
if ($action === 'borrar_varios') {
    $ids = $_POST['ids'] ?? [];
    if (!is_array($ids) || count($ids) === 0) { echo json_encode(['status'=>'error','msg'=>'No hay IDs']); exit; }
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $stmt = $conexion->prepare("DELETE FROM profesor_curso WHERE id IN ($placeholders)");
    if (!$stmt) { echo json_encode(['status'=>'error','msg'=>'Error prepare: '.$conexion->error]); exit; }
    // bind dinámico
    $params = [];
    $params[] = &$types;
    foreach ($ids as $i => $v) $params[] = &$ids[$i];
    call_user_func_array([$stmt, 'bind_param'], $params);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status'=>'ok','msg'=>'Asignaciones eliminadas']);
    exit;
}

/* BORRAR TODOS (TRUNCATE) */
if ($action === 'borrar_todos') {
    $conexion->query("TRUNCATE TABLE profesor_curso");
    echo json_encode(['status'=>'ok','msg'=>'Todas las asignaciones eliminadas']);
    exit;
}

echo json_encode(['status'=>'error','msg'=>'Acción no definida']);
exit;
