// assets/main.js
const AJAX_URL = "./ajax_alumnos.php";

document.addEventListener('DOMContentLoaded', ()=>{

  const tbody = document.getElementById('tbody-alumnos');
  const buscar = document.getElementById('buscar');
  const selectAll = document.getElementById('select-all');
  const btnGuardar = document.getElementById('btn-guardar');
  const btnBorrarSeleccionados = document.getElementById('btn-eliminar-seleccionados');
  const btnBorrarTodos = document.getElementById('btn-eliminar-todos');

  const modified = {};

  buscar.addEventListener('input', ()=>{
    const q = buscar.value.trim().toLowerCase();
    const filas = tbody.querySelectorAll('tr');
    filas.forEach(tr=>{
      const txt = tr.innerText.toLowerCase();
      tr.style.display = txt.includes(q) ? '' : 'none';
    });
  });

  selectAll.addEventListener('change', ()=>{
    const checked = selectAll.checked;
    tbody.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = checked);
  });

  tbody.addEventListener('change', (e)=>{
    if (e.target.classList.contains('row-checkbox')) {
      const all = [...tbody.querySelectorAll('.row-checkbox')];
      selectAll.checked = all.length && all.every(c=>c.checked);
    }
  });

  tbody.addEventListener('click', (e)=>{
    if (e.target.closest('.edit-row')) {
      const tr = e.target.closest('tr');
      makeRowEditable(tr);
    }

    if (e.target.closest('.delete-row')) {
      const tr = e.target.closest('tr');
      const id = tr.dataset.id;
      if (confirm('¿Eliminar alumno ID ' + id + '?')) {
        borrarUno(id, tr);
      }
    }

    if (e.target.closest('.pass-row')) {
      const tr = e.target.closest('tr');
      const id = tr.dataset.id;
      const nom = tr.querySelector('.cell-edit[data-field="nombres"]').innerText;
      const ape = tr.querySelector('.cell-edit[data-field="apellidos"]').innerText;
      openPassModal(id, nom, ape);
    }
  });

  function makeRowEditable(tr){
    if (tr.classList.contains('editing')) {
      location.reload();
      return;
    }
    tr.classList.add('editing');
    tr.querySelectorAll('.cell-edit').forEach(td=>{
      const field = td.dataset.field;
      if (field === 'id_curso') return;
      const text = td.innerText.trim();
      const input = document.createElement('input');
      input.type = (field === 'fecha_nacimiento') ? 'date' : 'text';
      input.value = text;
      input.className = 'cell-input';
      input.style.width = '100%';
      td.innerHTML = '';
      td.appendChild(input);
    });
    const btn = tr.querySelector('.edit-row');
    if (btn) btn.textContent = '✖ Cancelar';
  }

  tbody.addEventListener('blur', (e)=>{
    if (e.target.classList.contains('cell-input')) {
      const tr = e.target.closest('tr');
      markRowModified(tr);
    }
  }, true);

  tbody.addEventListener('change', (e)=>{
    if (e.target.classList.contains('curso-select')) {
      const tr = e.target.closest('tr');
      markRowModified(tr);
    }
  });

  function markRowModified(tr){
    const id = tr.dataset.id;
    const data = { id: id };
    tr.querySelectorAll('.cell-edit').forEach(td=>{
      const field = td.dataset.field;
      if (field === 'id_curso') {
        const sel = td.querySelector('.curso-select');
        data[field] = sel ? sel.value : '';
      } else {
        const input = td.querySelector('input');
        data[field] = input ? input.value.trim() : td.innerText.trim();
      }
    });
    modified[id] = data;
    tr.style.background = '#fff7e6';
  }

  btnGuardar.addEventListener('click', ()=>{
    const rows = Object.values(modified);
    if (rows.length === 0) return alert('No hay cambios para guardar.');
    if (!confirm('Guardar ' + rows.length + ' cambios?')) return;
    const form = new FormData();
    form.append('action', 'guardar_cambios');
    form.append('data', JSON.stringify(rows));
    fetch(AJAX_URL, { method:'POST', body: form })
      .then(r=>r.json()).then(resp=>{
        if (resp.status === 'ok') {
          alert(resp.msg);
          location.reload();
        } else alert('Error: ' + resp.msg);
      }).catch(err=> alert('Error al conectar: ' + err));
  });

  function borrarUno(id, tr){
    const form = new FormData();
    form.append('action','borrar_uno');
    form.append('id', id);
    fetch(AJAX_URL, { method:'POST', body: form })
      .then(r=>r.json()).then(resp=>{
        if (resp.status === 'ok') tr.remove();
        else alert('Error: ' + resp.msg);
      }).catch(()=>alert('Error al conectar'));
  }

  btnBorrarSeleccionados.addEventListener('click', ()=>{
    const ids = [...tbody.querySelectorAll('.row-checkbox:checked')].map(cb=>cb.dataset.id);
    if (ids.length === 0) return alert('No hay filas seleccionadas.');
    if (!confirm('Eliminar ' + ids.length + ' alumnos seleccionados?')) return;
    const form = new FormData();
    form.append('action','borrar_varios');
    ids.forEach(id => form.append('ids[]', id));
    fetch(AJAX_URL, { method:'POST', body: form })
      .then(r=>r.json()).then(resp=>{
        if (resp.status === 'ok') location.reload(); else alert('Error: ' + resp.msg);
      }).catch(()=>alert('Error al conectar'));
  });

  btnBorrarTodos.addEventListener('click', ()=>{
    if (!confirm('Eliminar TODOS los alumnos?')) return;
    const form = new FormData();
    form.append('action','borrar_todos');
    fetch(AJAX_URL, { method:'POST', body: form })
      .then(r=>r.json()).then(resp=>{
        if (resp.status === 'ok') location.reload(); else alert('Error: '+resp.msg);
      }).catch(()=>alert('Error al conectar'));
  });

  const modal = document.getElementById('modal-pass');
  const modalInfo = document.getElementById('modal-alumno-info');
  const modalInput = document.getElementById('modal-new-pass');
  const modalSave = document.getElementById('modal-save-pass');
  const modalCancel = document.getElementById('modal-cancel');
  let currentPassId = null;

  function openPassModal(id, nombres, apellidos) {
    currentPassId = id;
    modalInfo.textContent = `Alumno: ${nombres} ${apellidos} (ID ${id})`;
    modalInput.value = '';
    modal.classList.remove('hidden');
  }

  modalCancel.addEventListener('click', ()=> modal.classList.add('hidden'));

  modalSave.addEventListener('click', ()=>{
    const pw = modalInput.value.trim();
    if (pw.length < 4) return alert('Contraseña muy corta');
    const form = new FormData();
    form.append('action','change_password');
    form.append('id', currentPassId);
    form.append('password', pw);
    fetch(AJAX_URL, { method:'POST', body: form })
      .then(r=>r.json()).then(resp=>{
        if (resp.status === 'ok') {
          alert('Contraseña actualizada');
          modal.classList.add('hidden');
        } else alert('Error: ' + resp.msg);
      }).catch(()=>alert('Error al conectar'));
  });

});
