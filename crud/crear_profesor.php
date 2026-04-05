<?php
require "./config.php";

$nombres     = $_POST["nombres"];
$apellidos   = $_POST["apellidos"];
$dni         = $_POST["dni"];
$password    = password_hash($_POST["password"], PASSWORD_DEFAULT);
$email       = $_POST["email"];
$direccion   = $_POST["direccion"];
$telefono    = $_POST["telefono"];
$fecha       = $_POST["fecha_nacimiento"];


$sql = "INSERT INTO profesores (nombres, apellidos, dni, email, password, direccion, telefono, fecha_nacimiento)
        VALUES ('$nombres', '$apellidos', '$dni', '$email', '$password', '$direccion', '$telefono', '$fecha')";

if ($conexion->query($sql)) {
    echo "<script>
            alert('✅ Profesor creado con éxito');
            window.location.href = '../admin/agg_profesor.php'; 
          </script>";
} else {
    echo "<script>
            alert('❌ ERROR al guardar: ".$conexion->error."');
            window.location.href = '../admin/agg_profesor.php';
          </script>";
}
?>
