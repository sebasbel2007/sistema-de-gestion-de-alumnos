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
            <h2>Registro de cursos del plantel</h2>
            <p>En la siguiente plantilla puedes crear un curso</p>
        </div>

        <div class="form-box">
            <h3>Formulario para crear curso</h3>
            
            <form class="form-container" action="../crud/guardar_curso.php" method="POST">

                <div class="input-row">
                    <div class="input-box">
                        <label>Curso y division </label>
                        <input type="text" name="curso"placeholder="Ejemplo: 5-2"required>
                    </div>

                <button class="btn-submit" type="submit">Subir</button>

            </form>
        </div>
        
    </section>

<?php include '../footer.php'; ?>


</body>
</html>