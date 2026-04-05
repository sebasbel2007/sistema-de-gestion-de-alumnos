<?php
include 'config.php';
session_start();

if(!isset($_SESSION['profesor'])) {
    header("Location: login.php");
    exit();
}

foreach($_POST['nota1'] as $id => $valor){

    $n1 = $_POST['informe1'][$id];
    $n2 = $_POST['nota1'][$id];
    $n3 = $_POST['informe2'][$id];
    $n4 = $_POST['nota2'][$id];

    // Buscar si ya existe registro de notas de ese alumno
    $verificar = $conn->query("SELECT * FROM notas WHERE id_alumno = '$id'");

    if($verificar->num_rows > 0){

        $conn->query("INSERT INTO notas(id_alumno, nota1, nota2, nota3, nota4)
                      VALUES ('$id', '$n1', '$n2', '$n3', '$n4')");
    }
    else{
    echo "<script>alert('❌ No se pudo subir las notas'); history.back();</script>";
    }
}

header("Location: subir_notas.php?ok=1");
exit();
?>
