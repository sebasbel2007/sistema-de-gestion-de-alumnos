<?php
// gestion_alumno.php
// Conexión a la base
$conexion = new mysqli("localhost", "root", "", "sistema_escuela");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Traigo todos los alumnos (se muestra todo; la edición/guardado será por AJAX)
$sql = "SELECT id_materia, nombres FROM materias ORDER BY id_materia ASC";

$resultado = $conexion->query($sql);


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Panel de Gestión de Cursos</title>
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>

    <div  class="tablita">
        <header class="top-header">
            <h2>Panel de Gestión de Cursos</h2>
            <input type="search" id="buscar" placeholder="Buscar por nombre de curso..." autocomplete="off">
        </header>

        <main class="main-area">
            <div class="table-wrapper">
                <table id="tabla-alumnos">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>ID</th>
                            <th class="col-nombres">Nombres</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-alumnos">
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while($row = $resultado->fetch_assoc()): ?>
                            <tr data-id="<?= htmlspecialchars($row['id_materia']) ?>">
                                <td class="cell-checkbox"><input type="checkbox" class="row-checkbox" data-id="<?= $row['id_materia'] ?>"></td>
                                <td class="cell-id"><?= $row['id_materia'] ?></td>

                                <!-- Editable cells: data-field indica el nombre del campo -->
                                <td class="cell-edit" data-field="nombres"><?= htmlspecialchars($row['nombres']) ?></td>


                                <td class="cell-acciones">
                                    <button class="btn small edit-row" title="Editar fila">✏️ Editar</button>
                                    <button class="btn small delete-row" title="Eliminar fila">🗑️ Borrar</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="11" class="text-center">No hay cursos cargados</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <footer class="footer-bar">
                <button id="btn-eliminar-seleccionados" class="btn danger">ELIMINAR SELECCIONADOS</button>
                <button id="btn-eliminar-todos" class="btn danger">ELIMINAR TODOS</button>
                <button id="btn-guardar" class="btn primary">GUARDAR CAMBIOS</button>
            </footer>

            <script src="./assets/main.js"></script>




        </main>



    </div>
</body>
</html>
