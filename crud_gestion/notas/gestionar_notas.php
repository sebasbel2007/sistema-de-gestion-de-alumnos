<?php
session_start();
include '../../crud/config.php';


// Traer todos los cursos existentes
$sqlCursos = "SELECT id_curso, nombres AS curso FROM cursos ORDER BY nombres";
$resCursos = $conexion->query($sqlCursos);
$cursos = [];
if ($resCursos && $resCursos->num_rows > 0) {
    while ($c = $resCursos->fetch_assoc()) $cursos[] = $c;
}

// Recibir curso y materia seleccionados
$id_curso = $_GET['curso'] ?? null;
$id_materia = $_GET['materia'] ?? null;

// Si se elige curso, traer materias vinculadas a él
$materias = [];
if ($id_curso) {
    $sqlMaterias = "SELECT DISTINCT m.id_materia, m.nombres AS materia
                    FROM profesor_curso pc
                    INNER JOIN materias m ON pc.id_materia = m.id_materia
                    WHERE pc.id_curso = '$id_curso'
                    ORDER BY m.nombres";
    $resMats = $conexion->query($sqlMaterias);
    if ($resMats && $resMats->num_rows > 0) {
        while ($m = $resMats->fetch_assoc()) $materias[] = $m;
    }
}

// Obtener el ID del profesor asignado a este curso y materia
$id_profesor_asignado = null;
if ($id_curso && $id_materia) {
    $sqlProfesor = "SELECT id_profesor 
                    FROM profesor_curso 
                    WHERE id_curso = '$id_curso' AND id_materia = '$id_materia' 
                    LIMIT 1";
    $resProfe = $conexion->query($sqlProfesor);
    if ($resProfe && $resProfe->num_rows > 0) {
        $rowP = $resProfe->fetch_assoc();
        $id_profesor_asignado = $rowP['id_profesor'];
    }
}



// Traer alumnos si se seleccionó curso y materia
$alumnos = [];
if ($id_curso && $id_materia) {
    $sql = "SELECT a.id_alumno, a.nombres, a.apellidos,
                   n.informe1, n.nota1, n.informe2, n.nota2, n.promedio_final
            FROM alumnos a
            LEFT JOIN notas n 
                ON a.id_alumno = n.id_alumno 
                AND n.id_curso = '$id_curso' 
                AND n.id_materia = '$id_materia'
            WHERE a.id_curso = '$id_curso'
            ORDER BY a.apellidos, a.nombres";
    $res = $conexion->query($sql);
    if ($res) while ($r = $res->fetch_assoc()) $alumnos[] = $r;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión de Notas (Admin)</title>
    <link rel="stylesheet" href="./js/style.css">
</head>
<body>
<div  class="tablita">
    <header class="top-header">
        <h2>Gestión de Notas</h2>
        <form method="GET" style="display:flex; gap:8px; align-items:center;">
            <label>Curso:</label>
            <select name="curso" onchange="this.form.submit()">
                <option value="">Seleccionar curso...</option>
                <?php foreach($cursos as $c): ?>
                    <option value="<?= $c['id_curso'] ?>" <?= ($id_curso == $c['id_curso']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['curso']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($id_curso): ?>
            <label>Materia:</label>
            <select name="materia" onchange="this.form.submit()">
                <option value="">Seleccionar materia...</option>
                <?php foreach($materias as $m): ?>
                    <option value="<?= $m['id_materia'] ?>" <?= ($id_materia == $m['id_materia']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['materia']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
        </form>
    </header>

    <main class="main-area">
    <?php if (!$id_curso || !$id_materia): ?>
        <p style="padding:20px;">Seleccione un curso y una materia para comenzar a gestionar notas.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table id="tabla-notas">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>ID</th>
                        <th>Alumno</th>
                        <th>Informe 1</th>
                        <th>Nota 1</th>
                        <th>Informe 2</th>
                        <th>Nota 2</th>
                        <th>Promedio Final</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tbody-notas">
                    <?php if ($alumnos): ?>
                        <?php foreach($alumnos as $al): ?>
                        <tr data-id="<?= $al['id_alumno'] ?>">
                            <td><input type="checkbox" class="row-checkbox" data-id="<?= $al['id_alumno'] ?>"></td>
                            <td><?= $al['id_alumno'] ?></td>
                            <td><?= htmlspecialchars($al['apellidos'] . ' ' . $al['nombres']) ?></td>
                            <td>
                                <select class="informe1">
                                    <option value="">--</option>
                                    <option value="TEP" <?= ($al['informe1']=='TEP')?'selected':'' ?>>TEP</option>
                                    <option value="TEA" <?= ($al['informe1']=='TEA')?'selected':'' ?>>TEA</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="nota1" value="<?= $al['nota1'] ?>"></td>
                            <td>
                                <select class="informe2">
                                    <option value="">--</option>
                                    <option value="TEP" <?= ($al['informe2']=='TEP')?'selected':'' ?>>TEP</option>
                                    <option value="TEA" <?= ($al['informe2']=='TEA')?'selected':'' ?>>TEA</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="nota2" value="<?= $al['nota2'] ?>"></td>
                            <td><input type="number" step="0.01" class="promedio_final" value="<?= $al['promedio_final'] ?>" readonly></td>
                            <td><button class="btn small save-row">💾 Guardar</button></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center">No hay alumnos en este curso.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <script>
  const PROFESOR_ID = <?= json_encode($id_profesor_asignado ?? null) ?>;
  const CURSO_ID    = <?= json_encode($id_curso) ?>;
  const MATERIA_ID  = <?= json_encode($id_materia) ?>;
</script>


<script src="./js/main_notas_admin.js"></script>
    <footer class="footer-bar">
        <button id="btn-guardar-seleccionados" class="btn primary">GUARDAR SELECCIONADOS</button>
        <button id="btn-guardar-todos" class="btn primary">GUARDAR TODOS LOS CAMBIOS</button>
    </footer>
    </main>
</div>


</body>
</html>
