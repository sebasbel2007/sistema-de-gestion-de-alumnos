// main_notas.js (reemplazar)
const AJAX_URL = "./ajax_notas.php";

document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.getElementById('tbody-notas');
  const btnGuardarSeleccionados = document.getElementById('btn-guardar-seleccionados');
  const btnGuardarTodos = document.getElementById('btn-guardar-todos');

  // Guarda el estado inicial por fila para saber qué estaba ya completado
  const initialState = {}; // id_alumno -> { nota1: bool, nota2: bool, inf1: bool, inf2: bool }

  // Inicialización: bloquear inputs/selects que ya tengan valor
  document.querySelectorAll('#tbody-notas tr[data-id]').forEach(tr => {
    const id = tr.dataset.id;
    const nota1 = tr.querySelector('.nota1');
    const nota2 = tr.querySelector('.nota2');
    const inf1 = tr.querySelector('.informe1');
    const inf2 = tr.querySelector('.informe2');
    initialState[id] = {
      nota1: (nota1 && nota1.value !== ''),
      nota2: (nota2 && nota2.value !== ''),
      inf1:  (inf1  && inf1.value  !== ''),
      inf2:  (inf2  && inf2.value  !== '')
    };

    // bloquear visualmente y funcionalmente los que ya existían
    if (initialState[id].nota1 && nota1) { 
      nota1.readOnly = true; 
      nota1.classList.add('locked');
    }
    if (initialState[id].nota2 && nota2) { 
      nota2.readOnly = true; 
      nota2.classList.add('locked');
    }
    if (initialState[id].inf1 && inf1) { 
      inf1.disabled = true; 
      inf1.classList.add('locked');
    }
    if (initialState[id].inf2 && inf2) { 
      inf2.disabled = true; 
      inf2.classList.add('locked');
    }

    // Si no hay campos editables en la fila, deshabilitamos el botón guardar de la fila
    const saveBtn = tr.querySelector('.save-row');
    const anyEditable = !(initialState[id].nota1 && initialState[id].nota2 && initialState[id].inf1 && initialState[id].inf2);
    if (saveBtn) saveBtn.disabled = !anyEditable;
  });

  function obtenerDatosFila(tr) {
    return {
      id_alumno: tr.dataset.id,
      informe1: tr.querySelector('.informe1').value || '',
      nota1: tr.querySelector('.nota1').value || '',
      informe2: tr.querySelector('.informe2').value || '',
      nota2: tr.querySelector('.nota2').value || ''
    };
  }

  function guardarFilas(filas, onSuccess) {
    const form = new FormData();
    form.append('action', 'guardar_notas');
    form.append('id_profesor', PROFESOR_ID);
    form.append('id_curso', CURSO_ID);
    form.append('id_materia', MATERIA_ID);
    form.append('data', JSON.stringify(filas));

    fetch(AJAX_URL, { method: 'POST', body: form })
      .then(r => r.json())
      .then(resp => {
        if (resp.status === 'ok') {
          if (onSuccess) onSuccess(resp);
          else alert(resp.msg);
        } else {
          alert('Error: ' + resp.msg);
        }
      })
      .catch(err => alert('Error al conectar: ' + err));
  }

  // click guardar fila individual
  tbody.addEventListener('click', e => {
    if (e.target.classList.contains('save-row')) {
      const tr = e.target.closest('tr');
      // construir payload solo con los campos EDITABLES (no enviamos campos bloqueados)
      const payload = construirPayloadFilaSoloEditables(tr);
      if (!payload) return alert('No hay campos editables en esta fila.');
      guardarFilas([payload], () => {
        tr.style.background = '#e6ffed';
        setTimeout(()=> tr.style.background = '', 800);
        // después de guardar, bloquear los campos que ahora obtuvieron valor
        bloquearCamposSiCorresponde(tr);
      });
    }
  });

  btnGuardarSeleccionados.addEventListener('click', () => {
    const seleccionados = [...tbody.querySelectorAll('.row-checkbox:checked')].map(cb => cb.closest('tr'));
    const filasPayload = seleccionados.map(tr => construirPayloadFilaSoloEditables(tr)).filter(Boolean);
    if (filasPayload.length === 0) return alert('No hay campos editables en las filas seleccionadas.');
    guardarFilas(filasPayload, () => {
      alert('Seleccionados guardados');
      seleccionados.forEach(tr => { bloquearCamposSiCorresponde(tr); });
    });
  });

  btnGuardarTodos.addEventListener('click', () => {
    const todas = [...tbody.querySelectorAll('tr[data-id]')];
    const filasPayload = todas.map(tr => construirPayloadFilaSoloEditables(tr)).filter(Boolean);
    if (filasPayload.length === 0) return alert('No hay campos editables para guardar.');
    guardarFilas(filasPayload, () => {
      alert('Todas las filas guardadas');
      todas.forEach(tr => { bloquearCamposSiCorresponde(tr); });
    });
  });

  // cuando cambian las notas: actualizar promedio visualmente
  tbody.addEventListener('input', e => {
    const target = e.target;
    if (target.classList.contains('nota1') || target.classList.contains('nota2')) {
      const tr = target.closest('tr');
      const n1 = parseFloat(tr.querySelector('.nota1').value);
      const n2 = parseFloat(tr.querySelector('.nota2').value);
      const prom = (isFinite(n1) && isFinite(n2)) ? ((n1 + n2) / 2).toFixed(2) : '';
      tr.querySelector('.promedio_final').value = prom;
    }
  });

  // auto-save: solo si LOS CAMBIOS SON EN CAMPOS QUE ESTABAN VACIOS (no permitir override)
  tbody.addEventListener('blur', e => {
    const target = e.target;
    if (target.classList.contains('nota1') || target.classList.contains('nota2') || target.classList.contains('informe1') || target.classList.contains('informe2')) {
      const tr = target.closest('tr');
      const id = tr.dataset.id;

      // Si el campo estaba bloqueado inicialmente y cambió (no debería), revert y aviso
      if (campoEstabaBloqueadoInicialmente(target, id)) {
        // revertear valor al inicial (no permitimos cambiar)
        revertirValorBloqueado(target, id);
        alert('No podés modificar una nota ya cargada. Solo podés completar campos vacíos.');
        return;
      }

      // si ahora ambas notas son numéricas y al menos una de las dos fue ingresada ahora -> auto-save esa fila
      const n1 = parseFloat(tr.querySelector('.nota1').value);
      const n2 = parseFloat(tr.querySelector('.nota2').value);
      const ambos = isFinite(n1) && isFinite(n2);

      // solo autosave si alguna de las notas fue ingresada ahora (es decir, antes estaba vacía)
      const antesN1 = initialState[id] && initialState[id].nota1;
      const antesN2 = initialState[id] && initialState[id].nota2;
      const algunIngresoAhora = (!antesN1 && tr.querySelector('.nota1').value !== '') || (!antesN2 && tr.querySelector('.nota2').value !== '');

      if (ambos && algunIngresoAhora) {
        const fila = construirPayloadFilaSoloEditables(tr);
        if (fila) {
          guardarFilas([fila], () => {
            // actualizar promedio visualmente (servidor también lo guardó)
            const prom = ((n1 + n2) / 2).toFixed(2);
            tr.querySelector('.promedio_final').value = prom;
            bloquearCamposSiCorresponde(tr);
            tr.style.background = '#e6ffed';
            setTimeout(()=> tr.style.background = '', 900);
          });
        }
      }
    }
  }, true); // capture para blur

  // UTIL: construir payload con solo campos editables (no enviamos campos bloqueados)
  function construirPayloadFilaSoloEditables(tr) {
    const id = tr.dataset.id;
    const p = { id_alumno: id };
    let any = false;

    const inf1 = tr.querySelector('.informe1');
    const inf2 = tr.querySelector('.informe2');
    const n1 = tr.querySelector('.nota1');
    const n2 = tr.querySelector('.nota2');

    if (inf1 && !inf1.classList.contains('locked')) { p.informe1 = inf1.value || ''; any = true; }
    if (n1 && !n1.classList.contains('locked'))     { p.nota1 = n1.value || '';     any = true; }
    if (inf2 && !inf2.classList.contains('locked')) { p.informe2 = inf2.value || ''; any = true; }
    if (n2 && !n2.classList.contains('locked'))     { p.nota2 = n2.value || '';     any = true; }

    return any ? p : null;
  }

  // UTIL: bloquea campos que ahora tengan valor (lo que el profe acaba de ingresar)
  function bloquearCamposSiCorresponde(tr) {
    const id = tr.dataset.id;
    const inf1 = tr.querySelector('.informe1');
    const inf2 = tr.querySelector('.informe2');
    const n1 = tr.querySelector('.nota1');
    const n2 = tr.querySelector('.nota2');
    if (inf1 && inf1.value !== '') { inf1.disabled = true; inf1.classList.add('locked'); initialState[id].inf1 = true; }
    if (inf2 && inf2.value !== '') { inf2.disabled = true; inf2.classList.add('locked'); initialState[id].inf2 = true; }
    if (n1 && n1.value !== '')     { n1.readOnly = true; n1.classList.add('locked'); initialState[id].nota1 = true; }
    if (n2 && n2.value !== '')     { n2.readOnly = true; n2.classList.add('locked'); initialState[id].nota2 = true; }

    // deshabilitar botón si ya no quedan editables
    const saveBtn = tr.querySelector('.save-row');
    const anyEditable = !(initialState[id].nota1 && initialState[id].nota2 && initialState[id].inf1 && initialState[id].inf2);
    if (saveBtn) saveBtn.disabled = !anyEditable;
  }

  function campoEstabaBloqueadoInicialmente(target, id) {
    if (!id || !initialState[id]) return false;
    if (target.classList.contains('nota1')) return initialState[id].nota1;
    if (target.classList.contains('nota2')) return initialState[id].nota2;
    if (target.classList.contains('informe1')) return initialState[id].inf1;
    if (target.classList.contains('informe2')) return initialState[id].inf2;
    return false;
  }

  function revertirValorBloqueado(target, id) {
    // Si el campo estaba bloqueado inicialmente, devolvemos su valor original (si lo tenés en la DB en la UI,
    // ya está visible; pero por seguridad lo dejamos en '' o en el atributo value inicial).
    // Mejor: recargar la página para sincronizar si hay inconsistencias.
    // Aquí simple: deshacer cambio (setear al valor que ya está en initialState -> si true, lo dejamos como estaba; no podemos recuperar valor original si no lo guardamos).
    // Por simplicidad: recargamos la página cuando detectamos intento de sobreescritura.
    location.reload();
  }

});
