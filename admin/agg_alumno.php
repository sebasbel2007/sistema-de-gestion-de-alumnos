<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Alumno</title>

    <link rel="stylesheet" href="../css/agg_profesor.css">
    <link rel="stylesheet" href="../css/header.css" />
    <link rel="stylesheet" href="../css/footer.css" />
</head>
<body>

<?php include '../header_inicio.php'; ?>

<?php
// CONEXIÓN A BD
$conexion = new mysqli("localhost", "root", "", "sistema_escuela");

// VERIFICAR ERRORES
if ($conexion->connect_error) {
    echo "<p style='color:red;'>Error al conectar a la base de datos: " . $conexion->connect_error . "</p>";
}

// Carga de cursos
$cursos = $conexion->query("SELECT id_curso, nombres FROM cursos");
?>

<div class="dashboard">

    <div class="welcome">
        <h2>Registro de datos para alumnos del plantel</h2>
        <p>En la siguiente plantilla puedes subir los datos de un alumno</p>
    </div>

    <div class="form-box">
        <h3>Formulario para registrar alumnos</h3>

        <form action="../crud/crear_alumno.php" method="POST">

            <div class="form-grid">

                <div class="input-box">
                    <input type="text" name="nombres" placeholder="NOMBRES" required>
                </div>

                <div class="input-box">
                    <input type="text" name="apellidos" placeholder="APELLIDOS" required>
                </div>

                <div class="input-box">
                    <input type="number" name="dni" placeholder="DNI" required>
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="CONTRASEÑA" required>
                </div>

                <div class="input-box">
                    <input type="email" name="email" placeholder="EMAIL" required>
                </div>

                <div class="input-box">
                    <input type="text" name="direccion" placeholder="DIRECCIÓN">
                </div>

                <div class="input-box">
                    <input type="text" name="telefono" placeholder="TELÉFONO">
                </div>

                <div class="input-box">
                    <input type="date" name="fecha_nacimiento" placeholder="FECHA DE NACIMIENTO">
                </div>

                <!-- ✅ SELECT DE CURSOS CARGADOS DESDE BD -->
                <div class="input-box">
                    <select name="curso_id">
                        <option value="">-- Sin curso asignado --</option>

                        <?php if ($cursos && $cursos->num_rows > 0): ?>
                            <?php while ($c = $cursos->fetch_assoc()): ?>
                                <option value="<?= $c['id_curso']; ?>">
                                    <?= $c['nombres']; ?>
                                </option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option disabled>No hay cursos cargados</option>
                        <?php endif; ?>

                    </select>
                </div>

            </div> <!-- cierre form-grid -->

            <button class="btn-submit" type="submit">Subir</button>

        </form>
    </div>

</div>

<?php include '../footer.php'; ?>

</body>
</html>
