<?php
require "config.php";
$conexion = new mysqli("localhost", "root", "", "sistema_escuela");



// Obtener datos del formulario
$materia = $_POST['materia'];

// Crar curso
$sql = "INSERT INTO materias (nombres)
        VALUES ('$materia')";

if ($conexion->query($sql)) {
    echo "<script>
            alert('✅ Materia creada con éxito');
            window.location.href = '../admin/agg_materia.php'; 
          </script>";
} else {
    echo "<script>
            alert('❌ ERROR al Crear materia: ".$conexion->error."');
            window.location.href = '../admin/agg_materia.php';
          </script>";
}
?>


$conexion->close();
?>
