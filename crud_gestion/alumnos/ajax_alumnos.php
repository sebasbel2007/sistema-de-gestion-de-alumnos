<?php
// ajax_alumnos.php
header('Content-Type: application/json; charset=utf-8');

$conexion = new mysqli("localhost", "root", "", "sistema_escuela");
if ($conexion->connect_error) {
    echo json_encode(['status'=>'error','msg'=>'Error conexión DB']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'guardar_cambios') {
    $data = json_decode($_POST['data'] ?? '[]', true);
    if (!is_array($data)) {
        echo json_encode(['status'=>'error','msg'=>'Datos inválidos']); exit;
    }

    // Detectar nombre real de la columna (curso_id o id_curso)
    $colCheck = ['curso_id','id_curso'];
    $colName = null;
    foreach ($colCheck as $c) {
        $r = $conexion->query("SHOW COLUMNS FROM alumnos LIKE '$c'");
        if ($r && $r->num_rows > 0) { $colName = $c; break; }
    }
    if (!$colName) {
        echo json_encode(['status'=>'error','msg'=>'No existe columna curso (curso_id/id_curso)']); exit;
    }

    // Usaremos consultas por fila para manejar NULL correctamente
    foreach ($data as $row) {
        $id = intval($row['id'] ?? $row['id_alumno'] ?? 0);
        if ($id <= 0) continue;

        $nombres = $row['nombres'] ?? '';
        $apellidos = $row['apellidos'] ?? '';
        $dni = $row['dni'] ?? '';
        $email = $row['email'] ?? '';
        $direccion = $row['direccion'] ?? '';
        $telefono = $row['telefono'] ?? '';
        $fecha = $row['fecha_nacimiento'] ?? null;

        // normalizamos el valor del curso (puede ser '' o null o número)
        $valCursoRaw = $row['id_curso'] ?? $row['curso_id'] ?? '';
        $id_curso = ($valCursoRaw === '' || $valCursoRaw === null) ? null : intval($valCursoRaw);

        if ($id_curso === null) {
            // query que pone NULL explicitamente
            $sql = "UPDATE alumnos SET nombres=?, apellidos=?, dni=?, email=?, direccion=?, telefono=?, fecha_nacimiento=?, $colName = NULL WHERE id_alumno=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('sssssssi', $nombres, $apellidos, $dni, $email, $direccion, $telefono, $fecha, $id);
        } else {
            // query con id_curso como entero
            $sql = "UPDATE alumnos SET nombres=?, apellidos=?, dni=?, email=?, direccion=?, telefono=?, fecha_nacimiento=?, $colName = ? WHERE id_alumno=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('ssssssiii', $nombres, $apellidos, $dni, $email, $direccion, $telefono, $fecha, $id_curso, $id);
        }

        if (!$stmt) {
            echo json_encode(['status'=>'error','msg'=>'Error prepare: '.$conexion->error]); exit;
        }
        $ok = $stmt->execute();
        if (!$ok) {
            // opcional: loggear por fila
            echo json_encode(['status'=>'error','msg'=>'Error ejecutando update: '.$stmt->error]); exit;
        }
        $stmt->close();
    }

    echo json_encode(['status'=>'ok','msg'=>'Cambios guardados']);
    exit;
}


if ($action === 'borrar_uno') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) { echo json_encode(['status'=>'error','msg'=>'ID inválido']); exit; }
    $stmt = $conexion->prepare("DELETE FROM alumnos WHERE id_alumno = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status'=>'ok','msg'=>'Alumno eliminado']);
    exit;
}

if ($action === 'borrar_varios') {
    $ids = $_POST['ids'] ?? [];
    if (!is_array($ids) || count($ids) === 0) { echo json_encode(['status'=>'error','msg'=>'No hay IDs']); exit; }
    // preparar query con placeholders
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $stmt = $conexion->prepare("DELETE FROM alumnos WHERE id_alumno IN ($placeholders)");
    // bind params dynamically
    $refs = [];
    foreach ($ids as $k => $v) { $refs[$k] = intval($v); }
    $stmt_params = array_merge([&$types], array_map(function(&$v){ return $v; }, $refs));
    call_user_func_array([$stmt, 'bind_param'], $stmt_params);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status'=>'ok','msg'=>'Alumnos eliminados']);
    exit;
}

if ($action === 'borrar_todos') {
    $conexion->query("TRUNCATE TABLE alumnos");
    echo json_encode(['status'=>'ok','msg'=>'Todos los alumnos eliminados']);
    exit;
}

if ($action === 'change_password') {
    $id = intval($_POST['id'] ?? 0);
    $newpass = $_POST['password'] ?? '';
    if ($id <= 0 || $newpass === '') { echo json_encode(['status'=>'error','msg'=>'Datos inválidos']); exit; }
    $hash = password_hash($newpass, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("UPDATE alumnos SET password = ? WHERE id_alumno = ?");
    $stmt->bind_param('si', $hash, $id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status'=>'ok','msg'=>'Contraseña actualizada']);
    exit;
}

echo json_encode(['status'=>'error','msg'=>'Acción no definida']);
exit;
