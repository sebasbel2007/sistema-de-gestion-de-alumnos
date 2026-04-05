<?php
session_start();
$Usuario = $_POST['DNI'];
$Contraseña = $_POST['password'];

$conexion = mysqli_connect("localhost", "root", "", "sistema_escuela");

// 1️⃣ Buscar el usuario por DNI
$consulta = "SELECT * FROM alumnos WHERE dni = '$Usuario'";
$resultado = mysqli_query($conexion, $consulta);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    $hashGuardado = $fila['password'];

    // 2️⃣ Verificar contraseña
    if (password_verify($Contraseña, $hashGuardado)) {
        // 🧠 Guardar datos en la sesión
        $_SESSION['dni'] = $fila['dni'];
        $_SESSION['id_alumno'] = $fila['id_alumno'];
        $_SESSION['nombres'] = $fila['nombres'];
        $_SESSION['apellidos'] = $fila['apellidos'];
        $_SESSION['id_curso'] = $fila['id_curso'];

        // ✅ Redirigir correctamente (sin usar JavaScript)
        header("Location: ../alumno/dashboard.php");
        exit;
    } else {
        echo "<script>alert('❌ Contraseña incorrecta'); window.location.href='../inicio_sesion_alumno.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('❌ Usuario no encontrado'); window.location.href='../inicio_sesion_alumno.php';</script>";
    exit;
}
?>
