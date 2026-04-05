<?php
require "config.php";

$nombres = $_POST["nombres"];
$apellidos = $_POST["apellidos"];
$dni = $_POST["dni"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
$email = $_POST["email"];
$direccion = $_POST["direccion"];
$telefono = $_POST["telefono"];
$fecha = $_POST["fecha_nacimiento"];
$curso_id = $_POST["curso_id"]; // <-- nuevo dato

// Si no seleccionó curso, mandamos NULL
$curso_id = ($curso_id == "") ? "NULL" : "'$curso_id'";

$sql = "INSERT INTO alumnos (nombres, apellidos, dni, email, password, direccion, telefono, fecha_nacimiento, id_curso)
        VALUES ('$nombres', '$apellidos', '$dni', '$email', '$password', '$direccion', '$telefono', '$fecha', $curso_id)";

if ($conexion->query($sql)) {
    echo "<script>
            alert('✅ Alumno creado con éxito');
            window.location.href = '../admin/agg_alumno.php'; 
          </script>";
} else {
    echo "<script>
            alert('❌ ERROR al guardar: ". $conexion->error ."');
            window.location.href = '../admin/agg_alumno.php';
          </script>";
}
?>
