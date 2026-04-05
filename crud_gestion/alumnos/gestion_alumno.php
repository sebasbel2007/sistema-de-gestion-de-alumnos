<?php
// gestion_alumno.php
// Conexión a la base
$conexion = new mysqli("localhost", "root", "", "sistema_escuela");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Traigo todos los alumnos (se muestra todo; la edición/guardado será por AJAX)
$sql = "SELECT id_alumno, nombres, apellidos, dni, email, direccion, telefono, fecha_nacimiento, id_curso FROM alumnos ORDER BY id_alumno ASC";
$resultado = $conexion->query($sql);

// Opcional: traigo lista de cursos para los select (si tenés tabla cursos)
$cursos = [];
$resCursos = $conexion->query("SELECT id_curso, nombres FROM cursos ORDER BY id_curso ASC");
if ($resCursos) {
    while ($c = $resCursos->fetch_assoc()) $cursos[$c['id_curso']] = $c['nombres'];}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Panel de Gestión de Alumnos</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="top-header">
        <h2>Panel de Gestión de Alumnos</h2>
        <input type="search" id="buscar" placeholder="Buscar por nombre, DNI, email, dirección, curso..." autocomplete="off">
    </header>

    <main class="main-area">
        <div class="table-wrapper">
            <table id="tabla-alumnos">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>ID</th>
                        <th class="col-nombres">Nombres</th>
                        <th class="col-apellidos">Apellidos</th>
                        <th class="col-dni">DNI</th>
                        <th class="col-email">Email</th>
                        <th class="col-direccion">Dirección</th>
                        <th class="col-telefono">Teléfono</th>
                        <th class="col-fecha">Fecha Nac.</th>
                        <th class="col-curso">Curso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-alumnos">
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while($row = $resultado->fetch_assoc()): ?>
                        <tr data-id="<?= htmlspecialchars($row['id_alumno']) ?>">
                            <td class="cell-checkbox"><input type="checkbox" class="row-checkbox" data-id="<?= $row['id_alumno'] ?>"></td>
                            <td class="cell-id"><?= $row['id_alumno'] ?></td>

                            <!-- Editable cells: data-field indica el nombre del campo -->
                            <td class="cell-edit" data-field="nombres"><?= htmlspecialchars($row['nombres']) ?></td>
                            <td class="cell-edit" data-field="apellidos"><?= htmlspecialchars($row['apellidos']) ?></td>
                            <td class="cell-edit" data-field="dni"><?= htmlspecialchars($row['dni']) ?></td>
                            <td class="cell-edit" data-field="email"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="cell-edit" data-field="direccion"><?= htmlspecialchars($row['direccion']) ?></td>
                            <td class="cell-edit" data-field="telefono"><?= htmlspecialchars($row['telefono']) ?></td>
                            <td class="cell-edit" data-field="fecha_nacimiento"><?= htmlspecialchars($row['fecha_nacimiento']) ?></td>

                            <td class="cell-edit" data-field="id_curso">
                                <!-- select para curso; si no existe la lista de cursos, mostramos id actual -->
                                <?php if (!empty($cursos)): ?>
                                    <select class="curso-select">
                                        <option value="">--Sin curso--</option>
                                        <?php foreach ($cursos as $cid => $cname): ?>
                                            <option value="<?= $cid ?>" <?= ($cid == $row['id_curso']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cname) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <?= htmlspecialchars($row['id_curso']) ?>
                                <?php endif; ?>
                            </td>

                            <td class="cell-acciones">
                                <button class="btn small edit-row" title="Editar fila">✏️ Editar</button>
                                <button class="btn small delete-row" title="Eliminar fila">🗑️ Borrar</button>
                                <button class="btn small pass-row" title="Cambiar contraseña">🔐 Contraseña</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="11" class="text-center">No hay alumnos cargados</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="footer-bar">
        <button id="btn-eliminar-seleccionados" class="btn danger">ELIMINAR SELECCIONADOS</button>
        <button id="btn-eliminar-todos" class="btn danger">ELIMINAR TODOS</button>
        <button id="btn-guardar" class="btn primary">GUARDAR CAMBIOS</button>
    </footer>

    <!-- Modal cambio contraseña -->
    <div id="modal-pass" class="modal hidden">
        <div class="modal-content">
            <h3>Cambiar contraseña</h3>
            <p id="modal-alumno-info"></p>
            <label>Nueva contraseña</label>
            <input type="password" id="modal-new-pass" placeholder="Ingresar nueva contraseña">
            <div class="modal-actions">
                <button id="modal-save-pass" class="btn primary">Guardar</button>
                <button id="modal-cancel" class="btn">Cancelar</button>
            </div>
        </div>
    </div>
<script src="./assets/main.js"></script>

</body>
</html>
