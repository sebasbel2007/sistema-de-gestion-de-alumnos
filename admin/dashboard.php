<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/admin.css">
      <link rel="stylesheet" href="../css/header.css" />
  <link rel="stylesheet" href="../css/footer.css" />
</head>
<body>

<?php include '../header_inicio.php'; ?>


    <main class="dashboard">
        <div class="welcome">
            <h2>Hola Admin</h2>
            <p>Desde aquí puedes gestionar profesores, alumnos, cursos y boletines.</p>
        </div>

        <section class="cards-grid">

            <!-- PROFESORES -->
            <div class="card">
                <div class="card-header prof">
                    <h3>PROFESORES</h3>
                </div>
                <div class="card-body">
                    <div class="links">
                        <a href="../crud_gestion/profesores/gestion_profesores.php">Gestionar Profesores</a>
                        <a href="./agg_profesor.php">Agregar Profesor</a>
                    </div>

                    <div class="info-box">
                        <span>Cantidad de profesores</span>
                        <div class="info-number">
                            <img src="../img/icons8-teacher-100.png">
                            <p>25</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ALUMNOS -->
            <div class="card">
                <div class="card-header alum">
                    <h3>ALUMNOS</h3>
                </div>
                <div class="card-body">
                    <div class="links">
                        <a href="../crud_gestion/alumnos/gestion_alumno.php">Gestionar Alumnos</a>
                        <a href="./agg_alumno.php">Agregar Alumno</a>
                    </div>

                    <div class="info-box">
                        <span>Cantidad de alumnos</span>
                        <div class="info-number">
                            <img src="../img/icons8-alumno-100.png">
                            <p>400</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Años -->
            <div class="card">
                <div class="card-header curso">
                    <h3>AÑOS</h3>
                </div>
                <div class="card-body">
                    <div class="links">
                        <a href="../crud_gestion/cursos/gestion_cursos.php">Gestionar Años</a>
                        <a href="./agg_curso.php">Agregar Año</a>
                    </div>

                    <div class="info-box">
                        <span>Cantidad de cursos</span>
                        <div class="info-number">
                            <img src="../img/icons8-aula-100.png">
                            <p>15</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materias -->
            <div class="card">
                <div class="card-header bole">
                    <h3>MATERIAS</h3>
                </div>
                <div class="card-body">
                    <div class="links">
                        <a href="../crud_gestion/materias/gestion_materias.php">Gestionar materias</a>
                        <a href="./agg_materia.php">Crear materia</a>
                    </div>

                    <div class="info-box">
                        <span>Materias</span>
                        <div class="info-number">
                            <img src="../img/icons8-año-vista-100.png">
                            <p>15</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salones-->
            <div class="card">
                <div class="card-header boletines">
                    <h3>Boletines</h3>
                </div>
                <div class="card-body">
                    <div class="links">
                        <a href="../crud_gestion/notas/gestionar_notas.php">Gestionar Notas</a>


                    </div>

                    <div class="info-box">
                        <span>Materias</span>
                        <div class="info-number">
                            <img src="../img/icons8-año-vista-100.png">
                            <p>15</p>
                        </div>
                    </div>
                </div>
            </div>

                        <!-- Asignaciones-->
            <div class="card">
                <div class="card-header salones">
                    <h3>Asignaciones</h3>
                </div>
                <div class="card-body">
                    <div class="links">
                        <a href="../crud_gestion/asignaciones/gestion_asignaciones.php">Gestionar Asignaciones</a>
                        <a href="./agg_asignacion.php">ingresar Asignaciones <br> de manera masiva</a>


                    </div>

                    <div class="info-box">
                        <span>Materias</span>
                        <div class="info-number">
                            <img src="../img/icons8-año-vista-100.png">
                            <p>15</p>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main>


<?php include '../footer.php'; ?>


</body>
</html>