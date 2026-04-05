// assets/main_asignaciones.js
const AJAX_ASIG = "./ajax_asignaciones.php";

document.addEventListener('DOMContentLoaded', ()=>{

  const tbody = document.getElementById('tbody-asignaciones');
  const buscar = document.getElementById('buscar');
  const selectAll = document.getElementById('select-all');
  const btnGuardar = document.getElementById('btn-guardar');
  const btnBorrarSeleccionados = document.getElementById('btn-eliminar-seleccionados');
  const btnBorrarTodos = document.getElementById('btn-eliminar-todos');
  const formAgregar = document.getElementById('form-agregar-asignacion');

  // editar en memoria
  const modified = {};

  // busqueda en vivo
  buscar.addEventListener('input', ()=>{
    const q = buscar.value.trim().toLowerCase();
    [...tbody.querySelectorAll('tr')].forEach(tr=>{
      const txt = tr.innerText.toLowerCase();
      tr.style.display = txt.includes(q) ? '' : 'none';
    });
  });

  // select-all
  selectAll.addEventListener('change', ()=> {
    const checked = selectAll.checked;
    tbody.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = checked);
  });

  tbody.addEventListener('change', (e)=>{
    if (e.target.classList.contains('row-checkbox')) {
      const all = [...tbody.querySelectorAll('.row-checkbox')];
      selectAll.checked = all.length && all.every(c=>c.checked);
    }
  });

  // Clicks: editar/borrar
  tbody.addEventListener('click', (e)=>{
    if (e.target.closest('.edit-row')) {
      const tr = e.target.closest('tr');
      makeRowEditable(tr);
    }
    if (e.target.closest('.delete-row')) {
      const tr = e.target.closest('tr');
      const id = tr.dataset.id;
      if (confirm('Eliminar asignación ID ' + id + '?')) borrarUno(id, tr);
    }
  });

  // Crear asignación (form)
  formAgregar.addEventListener('submit', (ev)=>{
    ev.preventDefault();
    const fd = new FormData(formAgregar);
    const id_curso = fd.get('id_curso');
    const id_materia = fd.get('id_materia');
    const id_profesor = fd.get('id_profesor');
    if (!id_curso || !id_materia || !id_profesor) return alert('Complete todos los campos');

    const form = new FormData();
    form.append('action','crear');
    form.append('id_curso', id_curso);
    form.append('id_materia', id_materia);
    form.append('id_profesor', id_profesor);

    fetch(AJAX_ASIG, { method:'POST', body: form })
      .then(r=>r.json()).then(resp=>{
        if (resp.status === 'ok') {
          alert('Asignación creada');
          location.reload();
        } else alert('Error: ' + resp.msg);
      }).catch(()=>alert('Error al conectar'));
  });

  // hace editable una fila (convierte columnas a selects)
  function makeRowEditable(tr) {
    if (tr.classList.contains('editing')) {
      location.reload();
      return;
    }
    tr.classList.add('editing');

    // para cada cell-edit: reemplazar por select y marcar valor actual (data-value)
    tr.querySelectorAll('.cell-edit').forEach(td=>{
      const field = td.dataset.field;
      const currentVal = td.getAttribute('data-value') || '';
      const select = document.createElement('select');
      select.className = 'inline-select';
      select.style.width = '100%';

      if (field === 'curso') {
        // traer opciones del DOM (podés copiar el select del form)
        const optSource = document.getElementById('select-curso');
        cloneOptionsToSelect(optSource, select, currentVal);
      } else if (field === 'materia') {
        const optSource = document.getElementById('select-materia');
        cloneOptionsToSelect(optSource, select, currentVal);
      } else if (field === 'profesor') {
        const optSource = document.getElementById('select-profesor');
        cloneOptionsToSelect(optSource, select, currentVal);
      }
      td.innerHTML = '';
      td.appendChild(select);
    });

    const btn = tr.querySelector('.edit-row');
    if (btn) btn.textContent = '✖ Cancelar';
  }

  function cloneOptionsToSelect(sourceSelect, targetSelect, currentVal) {
    if (!sourceSelect) return;
    // clone options
    [...sourceSelect.options].forEach(op=>{
      const o = document.createElement('option');
      o.value = op.value;
      o.text = op.text;
      if (String(op.value) === String(currentVal)) o.selected = true;
      targetSelect.appendChild(o);
    });
    // listen change to mark modified
    targetSelect.addEventListener('change', (e)=>{
      const tr = targetSelect.closest('tr');
      markRowModified(tr);
    });
  }

  // on blur in inputs (no inputs here, only selects), we mark modified
  tbody.addEventListener('change', (e)=>{
    if (e.target.tagName.toLowerCase() === 'select') {
      const tr = e.target.closest('tr');
      markRowModified(tr);
    }
  });

  function markRowModified(tr) {
    const id = tr.dataset.id;
    const data = { id: id };
    tr.querySelectorAll('.cell-edit').forEach(td=>{
      const field = td.dataset.field; // curso / materia / profesor
      const sel = td.querySelector('select');
      data[field] = sel ? sel.value : td.innerText.trim();
    });
    modified[id] = data;
    tr.style.background = '#fff7e6';
  }

  // Guardar cambios en lote
  btnGuardar.addEventListener('click', ()=>{
    const rows = Object.values(modified);
    if (rows.length === 0) return alert('No hay cambios para guardar.');
    if (!confirm('Guardar ' + rows.length + ' cambios?')) return;
    const form = new FormData();
    form.append('action','guardar_cambios');
    form.append('data', JSON.stringify(rows));
    fetch(AJAX_ASIG, { method:'POST', body: form })
      .then(r=>r.json()).then(resp=>{
        if (resp.status === 'ok') { alert(resp.msg); location.reload(); }
        else alert('Error: ' + resp.msg);
      }).catch(()=>alert('Error al conectar'));
  });

  // borrar uno
  function borrarUno(id, tr) {
    const form = new FormData();
    form.append('action','borrar_uno');
    form.append('id', id);
    fetch(AJAX_ASIG, { method:'POST', body: form })
      .then(r=>r.json()).then(resp=>{
        if (resp.status === 'ok') tr.remove();
        else alert('Error: ' + resp.msg);
      }).catch(()=>alert('Error al conectar'));
  }

  // borrar seleccionados
  btnBorrarSeleccionados.addEventListener('click', ()=>{
    const ids = [...tbody.querySelectorAll('.row-checkbox:checked')].map(cb=>cb.dataset.id);
    if (ids.length === 0) return alert('No hay filas seleccionadas.');
    if (!confirm('Eliminar ' + ids.length + ' asignaciones seleccionadas?')) return;
    const form = new FormData();
    form.append('action','borrar_varios');
    ids.forEach(i => form.append('ids[]', i));
    fetch(AJAX_ASIG, { method:'POST', body: form })
      .then(r=>r.json()).then(resp=>{
        if (resp.status === 'ok') location.reload(); else alert('Error: ' + resp.msg);
      }).catch(()=>alert('Error al conectar'));
  });

  // borrar todos
  btnBorrarTodos.addEventListener('click', ()=>{
    if (!confirm('Eliminar TODAS las asignaciones?')) return;
    const form = new FormData();
    form.append('action','borrar_todos');
    fetch(AJAX_ASIG, { method:'POST', body: form })
      .then(r=>r.json()).then(resp=>{
        if (resp.status === 'ok') location.reload(); else alert('Error: ' + resp.msg);
      }).catch(()=>alert('Error al conectar'));
  });

});
