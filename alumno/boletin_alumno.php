<?php
// boletin_alumno.php
session_start();
require_once("../crud/config.php"); // debe definir $conexion (mysqli)

if (!isset($_SESSION['dni'])) {
    header("Location: ../inicio_sesion_alumno.php");
    exit;
}

// dni desde sesión (seteado en el login)
$dni_sesion = $_SESSION['dni'] ?? null;
if (!$dni_sesion) {
    echo "<p>Error: sesión inválida.</p>";
    exit;
}

// --- Obtener datos del alumno
$sqlAlumno = $conexion->prepare("SELECT id_alumno, nombres, apellidos, dni, id_curso FROM alumnos WHERE dni = ? LIMIT 1");
if (!$sqlAlumno) {
    die("Error preparando consulta de alumno: " . $conexion->error);
}
$sqlAlumno->bind_param("s", $dni_sesion);
$sqlAlumno->execute();
$resAlumno = $sqlAlumno->get_result();

if ($resAlumno->num_rows === 0) {
    echo "<p>No se encontró el alumno con ese DNI.</p>";
    exit;
}

$alumno = $resAlumno->fetch_assoc();
$id_alumno = (int)$alumno['id_alumno'];
$nombres   = $alumno['nombres'];
$apellidos = $alumno['apellidos'];
$dni       = $alumno['dni'];
$id_curso  = (int)$alumno['id_curso'];

// --- Obtener nombre del curso (ajusta campo 'nombres' o 'nombre' según tu tabla cursos)
$curso = 'Sin curso';
if ($id_curso > 0) {
    $sqlCurso = $conexion->prepare("SELECT nombres FROM cursos WHERE id_curso = ? LIMIT 1");
    if ($sqlCurso) {
        $sqlCurso->bind_param("i", $id_curso);
        $sqlCurso->execute();
        $resCurso = $sqlCurso->get_result();
        if ($resCurso && $resCurso->num_rows > 0) {
            $curso = $resCurso->fetch_assoc()['nombres'];
        }
    }
}

// --- Traer notas del alumno (puede devolver 0 filas)
$notasArray = []; // importante inicializar
$sqlNotas = "
  SELECT m.nombres AS materia,
         n.informe1, n.nota1, n.informe2, n.nota2, n.promedio_final
  FROM notas n
  LEFT JOIN materias m ON n.id_materia = m.id_materia
  WHERE n.id_alumno = ?
  ORDER BY m.nombres ASC
";
$stmt = $conexion->prepare($sqlNotas);
if ($stmt) {
    $stmt->bind_param("i", $id_alumno);
    $stmt->execute();
    $resNotas = $stmt->get_result();
    if ($resNotas) {
        while ($fila = $resNotas->fetch_assoc()) {
            $notasArray[] = $fila;
        }
    }
}

// calcular promedio general
$totalProm = 0;
$cantMaterias = 0;
foreach ($notasArray as $fila) {
    if (is_numeric($fila['promedio_final'])) {
        $totalProm += floatval($fila['promedio_final']);
        $cantMaterias++;
    }
}
$promedioGeneral = $cantMaterias > 0 ? round($totalProm / $cantMaterias, 2) : "-";

$fecha = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Boletín Estudiantil</title>
  <link rel="stylesheet" href="../css/estilo_boletin.css">
</head>
<body>

  <div class="hoja">

    <header class="encabezado">
      <div class="encabezado-izq">
        <img src="../img/loco_escuela.png" alt="Logo Colegio" class="logo">
      </div>
      <div class="encabezado-centro">
        <h2>Escuela Secundaria Técnica N°5</h2>
        <h3>Boletín Estudiantil</h3>
      </div>
      <div class="encabezado-der">
        <p><strong>Fecha:</strong> <?= htmlspecialchars($fecha) ?></p>
      </div>
    </header>

    <section class="datos-alumno">
      <p><strong>Alumno:</strong> <?= htmlspecialchars($apellidos . ", " . $nombres) ?></p>
      <p><strong>DNI:</strong> <?= htmlspecialchars($dni) ?></p>
      <p><strong>Curso:</strong> <?= htmlspecialchars($curso) ?></p>
    </section>

    <table class="tabla-boletin">
      <thead>
        <tr>
          <th>Materia</th>
          <th>Informe 1</th>
          <th>Nota 1</th>
          <th>Informe 2</th>
          <th>Nota 2</th>
          <th>Promedio Final</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($notasArray)): ?>
          <?php foreach ($notasArray as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['materia'] ?? 'Sin materia') ?></td>
              <td><?= htmlspecialchars($row['informe1'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['nota1'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['informe2'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['nota2'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['promedio_final'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6">Aún no hay notas cargadas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="promedio">
      <p><strong>Promedio general:</strong> <?= htmlspecialchars($promedioGeneral) ?></p>
    </div>

    <div class="firmas">
      <div class="firma">
        <img src="../img/icons8-firma-80.png" alt="Firma Director" class="img-firma">
        <p>Director/a</p>
      </div>
      <div class="firma">
        <img src="../img/icons8-sello-confidencial-100.png" alt="Sello Colegio" class="img-firma">
        <p>Sello Institucional</p>
      </div>
    </div>

    <div class="acciones">
      <button onclick="window.print()">🖨️ Imprimir / Guardar en PDF</button>
    </div>

  </div>

  <footer>
    <p>© <?= date('Y') ?> Escuela Secundaria Técnica N°5 - Sistema de Gestión Académica</p>
  </footer>

</body>
</html>
