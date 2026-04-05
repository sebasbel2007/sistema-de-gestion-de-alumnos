<?php
session_start();
include '../crud/config.php';

// --- ID del profesor desde sesión ---
$id_profesor = $_SESSION['id_profesor'] ?? null;
if (!$id_profesor) {
    echo "<script>alert('No se ha iniciado sesión correctamente.');window.location.href='../login.php';</script>";
    exit;
}

// --- Traer cursos y materias asignadas ---
$sql = "SELECT 
            c.id_curso,
            c.nombres AS curso,
            m.id_materia,
            m.nombres AS materia
        FROM profesor_curso pc
        INNER JOIN cursos c ON pc.id_curso = c.id_curso
        INNER JOIN materias m ON pc.id_materia = m.id_materia
        WHERE pc.id_profesor = '$id_profesor'
        ORDER BY c.nombres, m.nombres";

$result = $conexion->query($sql);

// --- Traer nombre del profesor desde la base de datos ---
$sql_prof = "SELECT nombres FROM profesores WHERE id_profesor = '$id_profesor' LIMIT 1";
$res_prof = $conexion->query($sql_prof);
if ($res_prof && $res_prof->num_rows > 0) {
    $nombre_profesor = $res_prof->fetch_assoc()['nombres'];
} else {
    $nombre_profesor = "Profesor";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Profesor</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<?php include '../header_inicio.php'; ?>

<main class="dashboard">
    <div class="welcome">
        <h2>Hola <?php echo htmlspecialchars($nombre_profesor); ?></h2>
        <p>Desde aquí puedes ver tus cursos y subir las notas de tus alumnos.</p>
    </div>

    <section class="cards-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header curso">
                        <h3><?= htmlspecialchars($row['materia']) ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="links">
                            <a href="subir_notas.php?id_curso=<?= $row['id_curso'] ?>&id_materia=<?= $row['id_materia'] ?>">
                                ➕ Subir Notas
                            </a>
                        </div>
                        <div class="info-box">
                            <span>Cursos</span>
                            <div class="info-number">
                            <img src="../img/icons8-aula-100.png">
                                
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No tienes asignaciones registradas aún.</p>
        <?php endif; ?>
    </section>
</main>

<?php include '../footer.php'; ?>

</body>
</html>
