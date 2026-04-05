<?php
session_start();
include '../crud/config.php';

$id_profesor = $_SESSION['id_profesor'] ?? null;
if (!$id_profesor) {
    echo "<script>alert('No se ha iniciado sesión correctamente.');window.location.href='../login.php';</script>";
    exit;
}

$id_curso = $_GET['curso'] ?? null;
$id_materia = $_GET['id_materia'] ?? null;


// Traer cursos y materias del profesor
$sqlCursos = "SELECT DISTINCT c.id_curso, c.nombres AS curso
              FROM profesor_curso pc
              INNER JOIN cursos c ON pc.id_curso = c.id_curso
              WHERE pc.id_profesor = '$id_profesor'";
$resCursos = $conexion->query($sqlCursos);

$cursos = [];
if ($resCursos && $resCursos->num_rows > 0) {
    while ($c = $resCursos->fetch_assoc()) $cursos[] = $c;
}

// Si el profesor elige un curso específico
$id_curso = $_GET['curso'] ?? null;
$alumnos = [];

if ($id_curso) {
    $sql = "SELECT a.id_alumno, a.nombres, a.apellidos, n.informe1, n.nota1, n.informe2, n.nota2, n.promedio_final
            FROM alumnos a
            LEFT JOIN notas n ON a.id_alumno = n.id_alumno AND n.id_profesor = '$id_profesor' AND n.id_curso = '$id_curso'
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
    <title>Subir Notas</title>
    <link rel="stylesheet" href="../crud_gestion/profesores/assets/style.css">
</head>
<body>
<header class="top-header">
    <h2>Subir Notas</h2>
    <form method="GET" style="display:flex; gap:8px; align-items:center;">
        <input type="hidden" name="id_materia" value="<?= htmlspecialchars($id_materia) ?>">
        <label>Curso:</label>
        <select name="curso" onchange="this.form.submit()">
            <option value="">Seleccionar curso...</option>
            <?php foreach($cursos as $c): ?>
                <option value="<?= $c['id_curso'] ?>" <?= ($id_curso == $c['id_curso']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['curso']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

</header>

<main class="main-area">
<?php if (!$id_curso): ?>
    <p style="padding:20px;">Seleccione un curso para comenzar a subir notas.</p>
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
</main>

<footer class="footer-bar">
    <button id="btn-guardar-seleccionados" class="btn primary">SUBIR SELECCIONADOS</button>
    <button id="btn-guardar-todos" class="btn primary">GUARDAR TODOS LOS CAMBIOS</button>
</footer>

<script>
  const PROFESOR_ID = <?= json_encode($id_profesor) ?>;
  const CURSO_ID    = <?= json_encode($id_curso) ?>;
  const MATERIA_ID  = <?= json_encode($id_materia ?? null) ?>; // null si no elegida
</script>



<script src="./js/main_notas.js"></script>




</body>
</html>
