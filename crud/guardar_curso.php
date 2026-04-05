<?php
require "config.php";

// Obtener datos del formulario
$curso = $_POST['curso'];

// Crar curso
$sql = "INSERT INTO cursos (nombres)
        VALUES ('$curso')";

if ($conexion->query($sql)) {
    echo "<script>
            alert('✅ Curso creado con éxito');
            window.location.href = '../admin/agg_curso.php'; 
          </script>";
} else {
    echo "<script>
            alert('❌ ERROR al guardar: ".$conexion->error."');
            window.location.href = '../admin/agg_curso.php';
          </script>";
}
?>


$conexion->close();
?>
