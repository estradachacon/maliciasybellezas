<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex">
                <h4 class="header-title">Listado de Encomendistas</h4>
                <?php if (tienePermiso('crear_encomendista')): ?>
                    <a class="btn btn-primary btn-sm ml-auto" href="<?= base_url('encomendistas/new') ?>"><i
                            class="fa-solid fa-plus"></i> Nuevo</a>
                <?php endif; ?>
            </div>
            <div class="card-body">

                <div class="row mb-3 align-items-end">

                    <div class="col-md-10">
                        <label for="searchInput">Buscar encomendista</label>
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Nombre o ID"
                                value="<?= esc($q ?? '') ?>">
                            <div class="input-group-append">
                                <span class="input-group-text" id="loading-spinner" style="display: none;">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                                <button class="btn btn-secondary" id="clearSearchBtn" style="display: none;">
                                    <i class="fa fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex justify-content-end align-items-center">
                            <label for="perPageSelect" class="mr-2 mb-0">Resultados:</label>
                            <select id="perPageSelect" class="form-control form-control-sm" style="width: 80px;">
                                <option value="5" <?= ($perPage == 5) ? 'selected' : '' ?>>5</option>
                                <option value="10" <?= ($perPage == 10) ? 'selected' : '' ?>>10</option>
                                <option value="20" <?= ($perPage == 20) ? 'selected' : '' ?>>20</option>
                                <option value="50" <?= ($perPage == 50) ? 'selected' : '' ?>>50</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="table-container">
                    <?= $this->include('encomendistas/_encom_table') ?>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableContainer = document.getElementById('table-container');
        const loadingSpinner = document.getElementById('loading-spinner');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const baseUrl = '<?= base_url('sellers/searchAjax') ?>';
        let searchTimeout;

        // Función para cargar los resultados (tabla)
        function loadResults(query, page = 1) {
            const perPage = document.getElementById('perPageSelect').value;

            const url = `${baseUrl}?q=${encodeURIComponent(query)}&page=${page}&perPage=${perPage}`;

            loadingSpinner.style.display = 'block';

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    tableContainer.innerHTML = html;
                    loadingSpinner.style.display = 'none';
                    updateClearButton(query);
                    rebindEvents();
                })
                .catch(() => {
                    loadingSpinner.style.display = 'none';
                    tableContainer.innerHTML = '<div class="alert alert-danger">Error al cargar los datos.</div>';
                });
        }

        // 🔥 Cuando cambias los resultados por página
        document.getElementById('perPageSelect').addEventListener('change', function() {
            const query = searchInput.value.trim();
            loadResults(query, 1);
        });

        // Re-adjuntar eventos (paginación y delete)
        function rebindEvents() {
            document.querySelectorAll('#pagination-links a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page');
                    const currentQuery = searchInput.value.trim();
                    loadResults(currentQuery, page);
                });
            });

            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.removeEventListener('click', handleDelete);
                button.addEventListener('click', handleDelete);
            });
        }

        // Búsqueda en vivo
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            searchTimeout = setTimeout(() => {
                loadResults(query);
            }, 300);
        });

        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            loadResults('');
            updateClearButton('');
        });

        function updateClearButton(query) {
            clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';
        }

        function handleDelete() {
            const id = this.dataset.id;

            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {

                if (!result.isConfirmed) return;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfHeader = document.querySelector('meta[name="csrf-header"]').getAttribute('content');

                fetch("<?= base_url('sellers/delete') ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest',
                            [csrfHeader]: csrfToken
                        },
                        body: new URLSearchParams({
                            id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {

                        //  CASO ÉXITO
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Éxito',
                                text: data.message,
                                icon: 'success',
                                timer: 1800,
                                showConfirmButton: false
                            });

                            const query = searchInput.value.trim();
                            loadResults(query);
                            return;
                        }

                        // CASO: TIENE PAQUETES
                        if (data.type === 'has_packages') {

                            let paquetes = data.paquetes || [];

                            let lista = paquetes.slice(0, 10).map(p => `
                    <li>
                        <b>#${p.id}</b> - ${p.tracking ?? ''} - ${p.cliente_nombre ?? ''}
                    </li>
                `).join('');

                            let extra = '';
                            if (paquetes.length > 10) {
                                extra = `<li><i>... y ${paquetes.length - 10} más</i></li>`;
                            }

                            Swal.fire({
                                title: 'No se puede eliminar',
                                icon: 'error',
                                html: `
                        <p class="mb-2">
                            Este vendedor tiene <b>${paquetes.length}</b> paquete(s) asociado(s):
                        </p>

                        <ul style="
                            text-align:left;
                            max-height:200px;
                            overflow:auto;
                            padding-left:20px;
                            font-size: 14px;
                        ">
                            ${lista}
                            ${extra}
                        </ul>

                        <div class="alert alert-warning mt-3">
                            Debe reasignar estos paquetes antes de eliminar.
                        </div>
                    `,
                                confirmButtonText: 'Entendido'
                            });

                            return;
                        }

                        // ERROR GENERAL
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'No se pudo eliminar el vendedor.',
                            icon: 'error'
                        });

                    })
                    .catch(() => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error de conexión con el servidor.',
                            icon: 'error'
                        });
                    });

            });
        }

        // Toggle detalles
        document.querySelectorAll('.toggle-details').forEach(btn => {
            btn.addEventListener('click', function() {
                const details = this.closest('.card').querySelector('.details');
                details.classList.toggle('d-none');
                this.textContent = details.classList.contains('d-none') ? 'Ver' : 'Ocultar';
            });
        });

        // Inicializar
        rebindEvents();
        updateClearButton(searchInput.value.trim());
    });
</script>

<?= $this->endSection() ?>