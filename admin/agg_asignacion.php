<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Asignaciones</title>
    <link rel="stylesheet" href="../css/agg_curso.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<?php include '../header_inicio.php'; ?>
<?php
    include '../crud/config.php';
    // Obtener cursos
    $queryCursos = "SELECT id_curso, nombres FROM cursos";
    $cursos = mysqli_query($conexion, $queryCursos);

    // Obtener materias
    $queryMaterias = "SELECT id_materia, nombres FROM materias";
    $materias = mysqli_query($conexion, $queryMaterias);

    // Obtener profesores
    $queryProfes = "SELECT id_profesor, nombres FROM profesores";
    $profesores = mysqli_query($conexion, $queryProfes);
?>

<section class="dashboard">
    <div class="welcome">
        <h2>Sistema de Crear Asignaciones</h2>
        <p>Selecciona el curso y asigna hasta 14 materias con sus respectivos profesores</p>
    </div>

    <div class="form-box">
        <h3>Formulario de Asignación</h3>

        <form class="form-container" action="../crud/guardar_asignaciones.php" method="POST">
            
            <!-- Seleccionar curso -->
            <div class="input-row">
                <div class="input-box">
                    <label>Seleccionar curso</label>
                    <select name="id_curso" required>
                        <option value="">-- Selecciona un curso --</option>
                        <?php while($curso = mysqli_fetch_assoc($cursos)) { ?>
                            <option value="<?= $curso['id_curso'] ?>"><?= $curso['nombres'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <hr style="margin: 25px 0;">

            <!-- Tabla de asignaciones -->
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left;">Materia</th>
                        <th style="text-align:left;">Profesor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i = 1; $i <= 14; $i++): ?>
                    <tr>
                        <td>
                            <select name="id_materia[]" required>
                                <option value="">-- Selecciona materia --</option>
                                <?php
                                mysqli_data_seek($materias, 0); // reiniciar puntero
                                while($materia = mysqli_fetch_assoc($materias)) {
                                    echo '<option value="'.$materia['id_materia'].'">'.$materia['nombres'].'</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <select name="id_profesor[]" required>
                                <option value="">-- Selecciona profesor --</option>
                                <?php
                                mysqli_data_seek($profesores, 0);
                                while($profe = mysqli_fetch_assoc($profesores)) {
                                    echo '<option value="'.$profe['id_profesor'].'">'.$profe['nombres'].'</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <button class="btn-submit" type="submit">Guardar Asignaciones</button>
        </form>
    </div>
</section>

<?php include '../footer.php'; ?>

</body>
</html>
