
<!-- Modal -->
<!--TODO add modal with edit film form properties -->

<!-- Modal -->
<div class="modal fade" id="edit-film-modal" tabindex="-1" aria-labelledby="editFilmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="edit-film-form">
        <div class="modal-header">
          <h5 class="modal-title" id="editFilmModalLabel">Edit Film</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="edit-film-id" />
          <div class="mb-3">
            <label for="edit-title" class="form-label">Title</label>
            <input type="text" class="form-control" id="edit-title" required />
          </div>
          <div class="mb-3">
            <label for="edit-description" class="form-label">Description</label>
            <textarea class="form-control" id="edit-description" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="edit-release-year" class="form-label">Release Year</label>
            <input type="number" class="form-control" id="edit-release-year" required />
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
  // Set this to your deployed backend root (not localhost)
  const API = 'https://127.0.0.1:5500/rest';  // adjust if your API is under /api or /rest

  // ---------- Helpers ----------
  async function apiGet(path) {
    const r = await fetch(`${API}${path}`);
    if (!r.ok) throw new Error(`GET ${path} -> ${r.status}`);
    return r.json();
  }

  async function apiJSON(path, method, data) {
    const r = await fetch(`${API}${path}`, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    if (!r.ok) throw new Error(`${method} ${path} -> ${r.status}: ${await r.text()}`);
    return r.json().catch(() => ({}));
  }

  async function apiDelete(path) {
    const r = await fetch(`${API}${path}`, { method: 'DELETE' });
    if (!r.ok) throw new Error(`DELETE ${path} -> ${r.status}: ${await r.text()}`);
    return true;
  }

  // ---------- State / Rendering ----------
  let rows = []; // currently shows category performance

  function renderTable() {
    const tbody = document.querySelector('#film-performance tbody');
    tbody.innerHTML = '';
    rows.forEach(row => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="text-center">
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-warning" data-action="edit" data-id="${row.id}">Edit</button>
            <button type="button" class="btn btn-danger"  data-action="delete" data-id="${row.id}">Delete</button>
          </div>
        </td>
        <td>${row.id}</td>
        <td>${row.name}</td>
        <td>${row.total}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  async function loadPerformance() {
    rows = await apiGet('/film/performance');
    renderTable();
  }

  // ---------- Service wired to your routes ----------
  window.FilmService = {
    async edit_film(id) {
      try {
        // Your backend returns a FILM for /film/:id (title, description, release_year)
        const film = await apiGet(`/film/${id}`);

        // Populate modal with film fields expected by PUT /film/edit/:id
        document.getElementById('edit-film-id').value = film.id;
        document.getElementById('edit-title').value = film.title || '';
        document.getElementById('edit-description').value = film.description || '';
        document.getElementById('edit-release-year').value = film.release_year || '';

        new bootstrap.Modal(document.getElementById('edit-film-modal')).show();
      } catch (e) {
        console.error(e);
        alert('Failed to load film for editing.');
      }
    },

    async delete_film(id) {
      if (!confirm('Are you sure you want to delete this film?')) return;
      try {
        await apiDelete(`/film/delete/${id}`);    // <-- matches your route
        await loadPerformance();                  // refresh table
      } catch (e) {
        console.error(e);
        alert('Delete failed.');
      }
    }
  };

  // ---------- Events ----------
  document.addEventListener('DOMContentLoaded', () => {
    loadPerformance().catch(err => {
      console.error(err);
      alert('Failed to load performance.');
    });

    document.querySelector('#film-performance tbody').addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-action]');
      if (!btn) return;
      const id = parseInt(btn.getAttribute('data-id'), 10);
      const action = btn.getAttribute('data-action');
      if (action === 'edit') FilmService.edit_film(id);
      if (action === 'delete') FilmService.delete_film(id);
    });

    // Save -> PUT /film/edit/:id with {title, description, release_year}
    document.getElementById('edit-film-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const id = parseInt(document.getElementById('edit-film-id').value, 10);
      const payload = {
        title: document.getElementById('edit-title').value.trim(),
        description: document.getElementById('edit-description').value.trim(),
        release_year: parseInt(document.getElementById('edit-release-year').value, 10)
      };

      try {
        await apiJSON(`/film/edit/${id}`, 'PUT', payload); // <-- matches your route
        await loadPerformance();
        const modalEl = document.getElementById('edit-film-modal');
        (bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)).hide();
      } catch (e) {
        console.error(e);
        alert('Save failed.');
      }
    });
  });
</script>
