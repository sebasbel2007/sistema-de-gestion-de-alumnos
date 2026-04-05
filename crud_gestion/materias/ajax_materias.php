<?php
header('Content-Type: application/json; charset=utf-8');

$conexion = new mysqli("localhost", "root", "", "sistema_escuela");
if ($conexion->connect_error) {
    echo json_encode(['status'=>'error','msg'=>'Error conexión DB']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

/* ✅ GUARDAR CAMBIOS */
if ($action === 'guardar_cambios') {
    $data = json_decode($_POST['data'] ?? '[]', true);
    if (!is_array($data)) {
        echo json_encode(['status'=>'error','msg'=>'Datos inválidos']); exit;
    }

    foreach ($data as $row) {

        $id = intval($row['id'] ?? $row['id_materia'] ?? 0);
        if ($id <= 0) continue;

        $nombres   = $row['nombres'] ?? '';

        $sql = "UPDATE materias
                SET nombres=? 
                WHERE id_materia=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('si', $nombres, $id);

        if (!$stmt->execute()) {
            echo json_encode(['status'=>'error','msg'=>'Error ejecutando update: '.$stmt->error]);
            exit;
        }
        $stmt->close();
    }

    echo json_encode(['status'=>'ok','msg'=>'Cambios guardados']);
    exit;
}

/* ✅ BORRAR UNO */
if ($action === 'borrar_uno') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) { echo json_encode(['status'=>'error','msg'=>'ID inválido']); exit; }

    $stmt = $conexion->prepare("DELETE FROM materias WHERE id_materia=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status'=>'ok','msg'=>'Curso eliminado']);
    exit;
}

/* ✅ BORRAR VARIOS */
if ($action === 'borrar_varios') {
    $ids = $_POST['ids'] ?? [];
    if (!is_array($ids) || count($ids) === 0) {
        echo json_encode(['status'=>'error','msg'=>'No hay IDs']); exit;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conexion->prepare("DELETE FROM materias WHERE id_materia IN ($placeholders)");

    $params = [];
    $params[] = &$types;
    foreach($ids as $i => $id) {
        $params[] = &$ids[$i];
    }
    call_user_func_array([$stmt, 'bind_param'], $params);

    $stmt->execute();
    $stmt->close();
    echo json_encode(['status'=>'ok','msg'=>'Cursos eliminados']);
    exit;
}

/* ✅ BORRAR TODOS */
if ($action === 'borrar_todos') {
    $conexion->query("TRUNCATE TABLE materias");
    echo json_encode(['status'=>'ok','msg'=>'Todos las materias eliminados']);
    exit;
}


echo json_encode(['status'=>'error','msg'=>'Acción no definida']);
exit;
