<?php
session_start(); // 👈 NECESARIO para usar $_SESSION

$Usuario = $_POST['DNI'];
$Contraseña = $_POST['password'];

$conexion = mysqli_connect("localhost", "root", "", "sistema_escuela");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// 1️⃣ Buscar al profesor por DNI
$consulta = "SELECT * FROM profesores WHERE dni = '$Usuario'";
$resultado = mysqli_query($conexion, $consulta);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    $hashGuardado = $fila['password'];

    // 2️⃣ Verificar contraseña
if (password_verify($Contraseña, $hashGuardado)) {
    // Iniciar sesión y guardar datos
    session_start();
    $_SESSION['id_profesor'] = $fila['id_profesor'];
    $_SESSION['dni'] = $fila['dni'];

    echo '<script>
        alert("✅ Bienvenido, ' . htmlspecialchars($fila['nombres']) . '");
        window.location.href="../profesores/profesor_dashboard.php";
    </script>';


    }
} else {
    echo '<script>
        alert("❌ Usuario no encontrado");
        window.location.href="../inicio_sesion_profe.php";
    </script>';
}
?>
