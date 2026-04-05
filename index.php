<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SM ACADEMIC SYSTEM</title>

  <!-- Google Font (Inter: sustituto profesional para "INTEL") -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="./css/style_index.css" />
  <link rel="stylesheet" href="./css/header.css" />
  <link rel="stylesheet" href="./css/footer.css" />


</head>
<body>

<?php include 'header_inicio.php'; ?>

  <main>
    <!-- Carrusel central -->
    <section class="hero">
      <div class="carousel" id="carousel">
        <button class="carousel-btn left" id="prevBtn" aria-label="Anterior">‹</button>

        <div class="carousel-track" id="track">
          <!-- ITEM 1 -->
          <div class="carousel-item">
            <div class="carousel-content">
              <h2>Escuela Secundaria Tecnica N°5</h2>
              <p>Nuestro colegio cuenta con espacios modernos, amplios y pensados para el aprendizaje. Aulas equipadas, laboratorios, biblioteca y áreas deportivas brindan un entorno seguro y cómodo. Aquí, cada estudiante encuentra un lugar para crecer y descubrir su potencial.</p>
              <!-- Reemplaza esta imagen por la que quieras. Mantener tamaño y relación. -->
              <div class="img-box"> <img class="f1" src="./img/instalaciones.png" alt=""></div>
              
            </div>
          </div>

          <!-- ITEM 2 -->
          <div class="carousel-item">
            <div class="carousel-content">
              <h2>Directivos</h2>
              <p>Un equipo de directivos comprometidos y docentes apasionados acompaña a los alumnos en cada etapa. Trabajamos con dedicación, cercanía y vocación de enseñanza. Creemos en el respeto, la inclusión y el progreso continuo de nuestra comunidad educativa.</p>
              <div class="img-box"> <img class="f1" src="./img/equipo.png" alt=""></div>
              
            </div>
          </div>

          <!-- ITEM 3 -->
          <div class="carousel-item">
            <div class="carousel-content">
              <h2>Equipamiento tecnológico</h2>
              <p>Disponemos de tecnología actualizada, salas de informática con equipos propios y herramientas profesionales para la formación técnica. Cada estudiante accede a recursos reales del entorno laboral. Aprender haciendo es nuestro camino hacia el futuro.</p>
              <div class="img-box"> <img class="f1" src="./img/pcs.png" alt=""></div>
              
            </div>
          </div>
        </div>

        <button class="carousel-btn right" id="nextBtn" aria-label="Siguiente">›</button>
      </div>

<script>
const track = document.getElementById("track");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");

const items = document.querySelectorAll(".carousel-item");
let index = 0;

function updateCarousel() {
    const width = items[0].offsetWidth;
    track.style.transform = `translateX(-${index * width}px)`;
}

nextBtn.addEventListener("click", () => {
    index = (index + 1) % items.length;
    updateCarousel();
});

prevBtn.addEventListener("click", () => {
    index = (index - 1 + items.length) % items.length;
    updateCarousel();
});

// Para que ajuste si la ventana cambia de tamaño
window.addEventListener("resize", updateCarousel);
</script>




      <div class="login-options">
        <h2>Iniciar Sesión</h2>
        <h2>Soporte</h2>

        <div class="login-card">

            <div class="login-links">
                <a href="./inicio_sesion_alumno.php" class="login-item">
                    <span>Alumno</span>
                    <span>></span>
                </a>
                <a href="./inicio_sesion_profe.php" class="login-item">
                    <span>Docente</span>
                    <span>></span>
                </a>
            </div>

            <div class="login-img">
                <img src="./img/loco_escuela.png" alt="Imagen login">
            </div>

        </div>
      </div>


    </section>

    <!-- Sección de tecnicaturas -->
    <section class="tecnicaturas">
      <h3>Tecnicaturas</h3>
      <div class="cards">
        <article class="card">
          <h4>Tecnico en Programación</h4>
          <p>Aprendé a escribir código, desarrollar aplicaciones y diseñar software.</p>
          <img src="./img/programacion.png" alt="">
        </article>

        <article class="card">
          <h4>Tecnico en Informatica</h4>
          <p>Instalación, configuración y mantenimiento de sistemas y redes.</p>
          <img src="./img/informatica.png" alt="">

        </article>

        <article class="card">
          <h4>Maestro Mayor de Obra</h4>
          <p>Dirección y supervisión de obras civiles, interpretación de planos.</p>
          <img src="./img/maestro_mo.png" alt="">

        </article>
      </div>
    </section>

<!-- Nosotros -->
<section class="nosotros">
  <div class="nosotros-wrapper">
    <div class="nosotros-text">
      <h3>Nosotros</h3>
      <p>
        La Escuela de Educación Secundaria Técnica N.º 5 es un referente académico reconocido en la región por la solidez de su formación técnica. 
        Nuestra misión es impulsar el desarrollo integral de cada estudiante, brindando herramientas reales para su futuro laboral y profesional.
      </p>
      <p>
        Creamos oportunidades. A través de la innovación tecnológica, el trabajo en equipo y el acompañamiento constante, formamos 
        jóvenes capaces, creativos y listos para contribuir al crecimiento de nuestro país.
      </p>
      <p>
        Si querés conocer nuestra propuesta educativa e iniciar el camino de tu hijo hacia un futuro con grandes posibilidades, 
        completá el formulario y te enviaremos toda la información necesaria.
      </p>


  </div>
</section>


<!-- Contacto -->
<section class="contacto" id="contacto">
  <h3>Contactanos</h3>
  <p class="intro-form">Completá el siguiente formulario y nos comunicaremos con vos a la brevedad.</p>

  <form id="contactForm" method="post" action="">
    
    <label>Nombre y Apellido
      <input type="text" name="name" required />
    </label>

    <label>Correo electrónico
      <input type="email" name="email" required />
    </label>

    <label>Asunto
      <input type="text" name="subject" required />
    </label>

    <label>Consulta
      <textarea name="message" rows="4" required></textarea>
    </label>

    <label class="checkbox">
      <input type="checkbox" name="privacy" required /> Acepto la política de privacidad
    </label>

    <div class="form-actions">
      <button type="submit" class="btn-send">Enviar mensaje</button>
    </div>
  </form>
  <script>
document.getElementById('contactForm').addEventListener('submit', function() {
    alert("✅ Mensaje enviado con éxito. ¡Gracias por contactarnos!");
});
</script>

</section>

  </main>

<?php include 'footer.php'; ?>



</body>
</html>
