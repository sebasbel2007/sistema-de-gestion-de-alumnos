const AJAX_URL = "./ajax_notas_admin.php";

document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.getElementById('tbody-notas');
  const btnGuardarSeleccionados = document.getElementById('btn-guardar-seleccionados');
  const btnGuardarTodos = document.getElementById('btn-guardar-todos');

  function construirPayloadFila(tr) {
    return {
      id_alumno: tr.dataset.id,
      informe1: tr.querySelector('.informe1').value || '',
      nota1: tr.querySelector('.nota1').value || '',
      informe2: tr.querySelector('.informe2').value || '',
      nota2: tr.querySelector('.nota2').value || ''
    };
  }

  function guardarFilas(filas) {
    const form = new FormData();
    form.append('action', 'guardar_notas');
    form.append('id_curso', CURSO_ID);
    form.append('id_materia', MATERIA_ID);
    form.append('id_profesor', PROFESOR_ID);
    form.append('data', JSON.stringify(filas));

    fetch(AJAX_URL, { method: 'POST', body: form })
      .then(r => r.json())
      .then(resp => {
        alert(resp.msg);
        if (resp.status === 'ok') location.reload();
      })
      .catch(err => alert('Error al conectar: ' + err));
  }

  tbody.addEventListener('click', e => {
    if (e.target.classList.contains('save-row')) {
      const tr = e.target.closest('tr');
      guardarFilas([construirPayloadFila(tr)]);
    }
  });

  btnGuardarSeleccionados.addEventListener('click', () => {
    const seleccionados = [...tbody.querySelectorAll('.row-checkbox:checked')].map(cb => cb.closest('tr'));
    if (seleccionados.length === 0) return alert('Seleccione al menos una fila.');
    const filas = seleccionados.map(tr => construirPayloadFila(tr));
    guardarFilas(filas);
  });

  btnGuardarTodos.addEventListener('click', () => {
    const filas = [...tbody.querySelectorAll('tr[data-id]')].map(tr => construirPayloadFila(tr));
    guardarFilas(filas);
  });

  // Calcular promedio al modificar notas
  tbody.addEventListener('input', e => {
    if (e.target.classList.contains('nota1') || e.target.classList.contains('nota2')) {
      const tr = e.target.closest('tr');
      const n1 = parseFloat(tr.querySelector('.nota1').value);
      const n2 = parseFloat(tr.querySelector('.nota2').value);
      const prom = (isFinite(n1) && isFinite(n2)) ? ((n1 + n2) / 2).toFixed(2) : '';
      tr.querySelector('.promedio_final').value = prom;
    }
  });
});
