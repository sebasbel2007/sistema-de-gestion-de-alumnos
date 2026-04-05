<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/agg_profesor.css">
      <link rel="stylesheet" href="../css/header.css" />
  <link rel="stylesheet" href="../css/footer.css" />
</head>
<body>

<?php include '../header_inicio.php'; ?>

    
    <div class="dashboard">

    <div class="welcome">
        <h2>Registro de datos para profesores del plantel</h2>
        <p>En la siguiente plantilla puedes subir los datos de un profesor</p>
    </div>

    <div class="form-box">
        <h3>Formulario para registrar profesores</h3>

        <form action="../crud/crear_profesor.php" method="POST">

            <div class="form-grid">

                <div class="input-box">
                    <input type="text" name="nombres" placeholder="NOMBRES" required>
                </div>

                <div class="input-box">
                    <input type="text" name="apellidos" placeholder="APELLIDOS" required>
                </div>

                <div class="input-box">
                    <input type="number" name="dni" placeholder="DNI" required>
                </div>

                <div class="input-box icon-right">
                    <input type="password" name="password" placeholder="CONTRASEÑA" required>
                </div>

                <div class="input-box icon-right">
                    <input type="email" name="email" placeholder="EMAIL" required>
                </div>

                <div class="input-box icon-right">
                    <input type="text" name="direccion" placeholder="DIRECCIÓN">
                </div>

                <div class="input-box icon-right">
                    <input type="number" name="telefono" placeholder="TELÉFONO">
                </div>

                <div class="input-box icon-right">
                    <input type="date" name="fecha_nacimiento" placeholder="FECHA">
                </div>


            </div>

            <button class="btn-submit">Subir</button>
        </form>

    </div>

</div>

<?php include '../footer.php'; ?>

    
</body>
</html>