<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/agg_curso.css">
      <link rel="stylesheet" href="../css/header.css" />
  <link rel="stylesheet" href="../css/footer.css" />

</head>
<body>

<?php include '../header_inicio.php'; ?>

    <section class="dashboard">

        <div class="welcome">
            <h2>Registro de las materias del plantel</h2>
            <p>En la siguiente plantilla puedes crear una nueva materia</p>
        </div>

        <div class="form-box">
            <h3>Formulario para crear materias </h3>
            
            <form class="form-container" action="../crud/guardar_materia.php" method="POST">

                <div class="input-row">
                    <div class="input-box">
                        <label>Materia </label>
                        <input type="text" name="materia"placeholder="Ejemplo: Matematicas"required>
                    </div>

                <button class="btn-submit" type="submit">Subir</button>

            </form>
        </div>
        
    </section>

<?php include '../footer.php'; ?>


</body>
</html>