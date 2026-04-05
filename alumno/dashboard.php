
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/alumno.css">
    <link rel="stylesheet" href="../css/header.css" />
  <link rel="stylesheet" href="../css/footer.css" />
</head>
<body>
<?php include '../header_inicio.php'; ?>


    <main class="dashboard">

        <div class="welcome">
            <h2>Hola querido <span>Usuario</span></h2>
            <p>Desde aquí puedes revisar tus notas.</p>
        </div>

        <section class="cards-grid">

            <div class="card">
                <div class="card-body">
                    
                    <!-- BOTÓN -->
                    <div class="links">
                        <a class="btn-boletin" href="./boletin_alumno.php">Ver mi Boletín</a>
                    </div>

                    <!-- INFO DE MATERIAS -->
                    <div class="info-box">
                        <span>Materias aprobadas</span>
                        <div class="info-number">
                            <img src="../img/icons8-año-vista-100.png">
                            <p>8/10</p>
                        </div>
                    </div>

                </div>
            </div>

        </section>

    </main>

<?php include '../footer.php'; ?>


</body>
</html>