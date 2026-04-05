<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/inicio_sesion.css">
  <link rel="stylesheet" href="./css/header.css" />
  <link rel="stylesheet" href="./css/footer.css" />
    <title>Document</title>
</head>
<body>

<?php include 'header_inicio.php'; ?>

<div class="cuerpo">

    <main class="login-container">
        <div class="welcome-box">
        <p><strong>Saludos Profesor</strong><br>
        Por favor inicie sesión para acceder a nuestro Sistema Educativo</p>
        </div>

        <form class="login-box" action="crud/inicio_sesion_profesor.php" method="POST">
            <h2>Iniciar sesión para profesores</h2>

            <div class="input-group">
                <label for="dni">DNI</label>
                <input type="number" id="dni" placeholder="Ingrese su DNI" name="DNI" required>
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" placeholder="Ingrese su contraseña" name="password" required>
                <a href="./recuperar_password" class="forgot">Olvidé mi contraseña</a>
            </div>

            <button type="submit">Aceptar</button>
        </form>
    </main>
</div>
    



<?php include 'footer.php'; ?>





    
</body>
</html>