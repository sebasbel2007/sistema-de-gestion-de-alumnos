<?php
// gestion_asignaciones.php
// Conexión
$conexion = new mysqli("localhost", "root", "", "sistema_escuela");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Traer cursos, materias y profesores para los selects
$cursos = $conexion->query("SELECT id_curso, nombres FROM cursos ORDER BY id_curso");
$materias = $conexion->query("SELECT id_materia, nombres FROM materias ORDER BY id_materia");
$profes = $conexion->query("SELECT id_profesor, CONCAT(nombres,' ',apellidos) as profesor FROM profesores ORDER BY id_profesor");

// Traer asignaciones actuales (JOIN para mostrar nombres legibles)
$sql = "SELECT pc.id, pc.id_profesor, pc.id_curso, pc.id_materia,
               c.nombres AS curso, m.nombres AS materia, CONCAT(p.nombres,' ',p.apellidos) AS profesor
        FROM profesor_curso pc
        LEFT JOIN cursos c ON pc.id_curso = c.id_curso
        LEFT JOIN materias m ON pc.id_materia = m.id_materia
        LEFT JOIN profesores p ON pc.id_profesor = p.id_profesor
        ORDER BY pc.id ASC";

$asignaciones = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestionar Asignaciones (Curso - Materia - Profesor)</title>
    <link rel="stylesheet" href="./assets/style.css"> <!-- ajusta ruta si es necesario -->
</head>
<body>
    <header class="top-header">
        <h2>Gestionar Asignaciones</h2>
        <input type="search" id="buscar" placeholder="Buscar por curso, materia o profesor..." autocomplete="off">
    </header>

    <main class="main-area">
      <div class="table-wrapper" style="padding:16px;">
        <!-- FORM: agregar asignación -->
        <div style="background:#fff;padding:12px;border-radius:8px;margin-bottom:12px;border:1px solid #eee;">
            <h3 style="margin:0 0 8px 0;">Agregar asignación (Curso → Materia → Profesor)</h3>
            <form id="form-agregar-asignacion">
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    <select name="id_curso" id="select-curso" required>
                        <option value="">Seleccionar curso...</option>
                        <?php while($r = $cursos->fetch_assoc()): ?>
                          <option value="<?= $r['id_curso'] ?>"><?= htmlspecialchars($r['nombres']) ?></option>
                        <?php endwhile; ?>
                    </select>

                    <select name="id_materia" id="select-materia" required>
                        <option value="">Seleccionar materia...</option>
                        <?php while($r = $materias->fetch_assoc()): ?>
                          <option value="<?= $r['id_materia'] ?>"><?= htmlspecialchars($r['nombres']) ?></option>
                        <?php endwhile; ?>
                    </select>

                    <select name="id_profesor" id="select-profesor" required>
                        <option value="">Seleccionar profesor...</option>
                        <?php while($r = $profes->fetch_assoc()): ?>
                          <option value="<?= $r['id_profesor'] ?>"><?= htmlspecialchars($r['profesor']) ?></option>
                        <?php endwhile; ?>
                    </select>

                    <button type="submit" class="btn primary">Agregar asignación</button>
                </div>
            </form>
        </div>

        <!-- TABLA: asignaciones -->
        <table id="tabla-asignaciones" style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:linear-gradient(180deg,var(--primary), #1749b8); color:#fff;">
                    <th style="width:48px;"><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Curso</th>
                    <th>Materia</th>
                    <th>Profesor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tbody-asignaciones">
                <?php if ($asignaciones && $asignaciones->num_rows > 0): ?>
                    <?php while($row = $asignaciones->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td class="cell-checkbox"><input type="checkbox" class="row-checkbox" data-id="<?= $row['id'] ?>"></td>
                            <td class="cell-id"><?= $row['id'] ?></td>
                            <td class="cell-edit" data-field="curso" data-value="<?= htmlspecialchars($row['id_curso']) ?>"><?= htmlspecialchars($row['curso']) ?></td>
                            <td class="cell-edit" data-field="materia" data-value="<?= htmlspecialchars($row['id_materia']) ?>"><?= htmlspecialchars($row['materia']) ?></td>
                            <td class="cell-edit" data-field="profesor" data-value="<?= htmlspecialchars($row['id_profesor']) ?>"><?= htmlspecialchars($row['profesor']) ?></td>
                            <td class="cell-acciones">
                                <button class="btn small edit-row">✏️ Editar</button>
                                <button class="btn small delete-row">🗑️ Borrar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay asignaciones cargadas</td></tr>
                <?php endif; ?>
            </tbody>
        </table>



      </div>
              <!-- Footer con botones -->
    <footer class="footer-bar">
        <button id="btn-eliminar-seleccionados" class="btn danger">ELIMINAR SELECCIONADOS</button>
        <button id="btn-eliminar-todos" class="btn danger">ELIMINAR TODOS</button>
        <button id="btn-guardar" class="btn primary">GUARDAR CAMBIOS</button>
    </footer>
    </main>

    <script src="assets/main_asignaciones.js"></script>
</body>
</html>
