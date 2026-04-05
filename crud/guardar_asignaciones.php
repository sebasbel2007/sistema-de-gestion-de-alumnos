<?php
include './config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_curso = $_POST['id_curso'];
    $materias = $_POST['id_materia'];
    $profesores = $_POST['id_profesor'];

    $errores = 0;

    for ($i = 0; $i < count($materias); $i++) {
        if (!empty($materias[$i]) && !empty($profesores[$i])) {
            $materia = $materias[$i];
            $profesor = $profesores[$i];

            $query = "INSERT INTO profesor_curso (id_profesor, id_curso, id_materia)
                      VALUES ('$profesor', '$id_curso', '$materia')";
            
            if (!mysqli_query($conexion, $query)) {
                $errores++;
            }
        }
    }

    if ($errores === 0) {
        echo "<script>
                alert('✅ Asignaciones creadas con éxito');
                window.location.href = '../admin/agg_asignacion.php';
              </script>";
    } else {
        echo "<script>
                alert('⚠️ Algunas asignaciones no se guardaron correctamente');
                window.location.href = '../admin/agg_asignacion.php';
              </script>";
    }
    exit;
}
?>
