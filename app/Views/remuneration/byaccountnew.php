<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<script>
    let currentView = localStorage.getItem('packageView') || 'grid';
</script>
<style>
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        min-height: 40px;
        z-index: 1050;
        pointer-events: none;
    }

    .toast {
        pointer-events: auto;
        min-width: 250px;
    }

    .select2-container {
        z-index: 1060;
    }

    .toast.show {
        opacity: 1;
        transition: opacity 0.5s ease-in-out;
    }

    #payment-cart {
        position: fixed;
        bottom: 20px;
        right: 20px;
        max-width: 320px;
        z-index: 1050;
        /* menor que select2 */
    }

    .payment-cart {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 320px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, .2);
        z-index: 2000;
        overflow: hidden;
        transition: all .3s ease;
    }

    .payment-cart.minimized .cart-body {
        display: none;
    }

    .cart-header {
        background: #198754;
        color: #fff;
        padding: 14px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cart-total {
        font-size: 1.4rem;
        font-weight: bold;
    }

    .cart-count {
        background: #fff;
        color: #198754;
        border-radius: 50%;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .cart-body {
        max-height: 400px;
        overflow-y: auto;
    }

    .cart-footer {
        padding: 10px;
        border-top: 1px solid #eee;
    }

    .package-card {
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .package-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, .15);
    }

    .package-footer {
        background: #f8f9fa;
        padding: 10px;
        border-top: 1px solid #eee;
        font-size: .9rem;
    }

    .package-amount {
        font-size: 1.2rem;
        font-weight: bold;
        color: #198754;
    }

    .package-discount {
        color: #dc3545;
        font-size: .85rem;
    }

    #cart-items .list-group-item:hover {
        background-color: #f8f9fa;
    }

    /* Aminacion en carrito*/
    .package-added {
        animation: packageGlow 0.6s ease;
    }

    @keyframes packageGlow {
        0% {
            box-shadow: 0 0 0px rgba(25, 135, 84, 0);
            transform: scale(1);
        }

        40% {
            box-shadow: 0 0 25px rgba(25, 135, 84, 0.8);
            transform: scale(1.03);
        }

        100% {
            box-shadow: 0 0 0px rgba(25, 135, 84, 0);
            transform: scale(1);
        }
    }

    /* Animación carrito */
    .cart-glow {
        animation: cartGlow 0.8s ease;
    }

    @keyframes cartGlow {
        0% {
            box-shadow: 0 0 0 rgba(25, 135, 84, 0);
        }

        40% {
            box-shadow: 0 0 35px rgba(25, 135, 84, 0.9);
        }

        100% {
            box-shadow: 0 0 0 rgba(25, 135, 84, 0);
        }
    }

    /* Modal de paquetes solo flete */
    .modal {
        z-index: 1050;
    }

    .modal-backdrop {
        z-index: 1040;
    }

    .modal {
        z-index: 3000 !important;
    }

    .modal-backdrop {
        z-index: 2999 !important;
    }

    #modalFletes .card {
        transition: all .2s ease;
    }

    #modalFletes .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, .2);
    }

    /* ===== EFECTO SELECCIÓN SUAVE PRO ===== */

    #modalFletes .package-selectable {
        cursor: pointer;
        border: 1px solid #dee2e6;
        transition:
            border-color .25s ease,
            box-shadow .25s ease,
            background-color .25s ease;
    }

    /* Hover sutil */
    #modalFletes .package-selectable:hover {
        box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
    }

    /* Seleccionado elegante */
    #modalFletes .package-selectable.selected {
        border-color: #28a745 !important;
        background-color: rgba(40, 167, 69, 0.04);
        box-shadow:
            0 0 0 3px rgba(40, 167, 69, 0.15);
    }

    /* ===== PAQUETE YA AGREGADO ===== */
    .package-selected {
        border: 2px solid #198754 !important;
        background-color: rgba(25, 135, 84, 0.08);
    }

    .package-selected .package-amount,
    .package-selected strong {
        color: #198754 !important;
    }

    .package-selected::after {
        content: "✓ Agregado";
        position: absolute;
        top: 10px;
        right: 10px;
        background: #198754;
        color: #fff;
        font-size: 0.75rem;
        padding: 3px 8px;
        border-radius: 12px;
    }
</style>
<link rel="stylesheet" href="<?= base_url('backend/assets/css/newpackage.css') ?>">

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <h5 class=" header-title mb-0">Registrar remuneración por tipo de cuenta</h5>
                <a href="<?= base_url('packages/return') ?>" class="btn btn-light btn-sm">Volver</a>
            </div>
            <div class="card-body">
                <form id="formPaquete" enctype="multipart/form-data">
                    <div class="row g-3 mt-1 mb-1">
                        <div class="col-md-6">
                            <label for="seller_id" class="form-label">Vendedor</label>
                            <select id="seller_id" name="seller_id" class="form-select" style="width: 100%;" required>
                                <option value=""></option>
                            </select>
                            <small class="form-text text-muted">Escribí para buscar un vendedor.</small>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="p-2 w-50 rounded shadow-sm text-center"
                                style="background-color: #f8f9fa; border: 1px solid #dee2e6; height: 80px;">
                                <small class="text-muted d-block fw-bold">
                                    <strong>Seleccione una cuenta</strong>
                                </small>

                                <div id="account-selector">
                                    <select name="cuenta_asignada"
                                        class="form-control select2-account">
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <input type="hidden" name="user_id" value="<?= session('id') ?>">
                            <input type="hidden" id="payment-type" value="account">
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <button type="button"
                            id="btnVerFletes"
                            class="btn btn-outline-warning btn-sm d-none">
                            Ver fletes pendientes
                        </button>
                    </div>
                    <div class="d-flex justify-content-end mb-3">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="viewGrid">
                                Vista Fotos
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="viewList">
                                Vista Lista
                            </button>
                        </div>
                    </div>
                    <div class="text-end">
                        <div id="packages-container" class="row g-3">
                            <!-- Aquí se renderizan las tarjetas -->
                        </div>
                        <hr>
                        <hr>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Floating Cart -->
<div id="payment-cart" class="payment-cart minimized">

    <!-- Header (burbuja) -->
    <div class="cart-header" id="cartToggle">
        <div>
            <strong>Total</strong>
            <div class="cart-total">$0.00</div>
        </div>
        <div class="cart-count">0</div>
    </div>

    <!-- Body (carrito expandido) -->
    <div class="cart-body">
        <ul id="cart-items" class="list-group list-group-flush"></ul>

        <div class="cart-footer">
            <button id="btnPay" class="btn btn-success w-100">
                Pagar remuneración
            </button>
        </div>
    </div>
</div>
<!-- Modal Fletes Pendientes -->
<div class="modal fade" id="modalFletes" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Fletes pendientes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-sm btn-outline-secondary" id="selectAllFletes">
                        Seleccionar todos
                    </button>
                </div>

                <div class="row g-3" id="fletes-container">
                    <!-- Aquí se renderizan cards -->
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">
                    Cerrar
                </button>
                <button class="btn btn-warning" id="agregarFletes">
                    Agregar seleccionados
                </button>
            </div>

        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Evitar que scroll cambie los números
        document.querySelectorAll('input[type=number]').forEach(input => {
            input.addEventListener('wheel', function(e) {
                e.preventDefault();
            });
        });

        /* -----------------------------------------------------------
         * SELECT2 – Vendedores
         * ----------------------------------------------------------- */
        $('#seller_id').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#formPaquete'),
            placeholder: '🔍 Buscar vendedor...',
            allowClear: true,
            minimumInputLength: 2,
            width: '100%',
            language: {
                inputTooShort: function(args) {
                    let remaining = args.minimum - args.input.length;
                    return `Por favor ingrese ${remaining} caracter${remaining === 1 ? '' : 'es'} o más`;
                },
                searching: function() {
                    return "Buscando...";
                },
                noResults: function() {
                    return "No se encontraron resultados";
                }
            },
            ajax: {
                url: '<?= base_url('sellers-search') ?>',
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term
                }),
                processResults: function(data, params) {
                    let results = data || [];
                    return {
                        results
                    };
                },
                cache: true
            }
        });
    });
</script>
<script>
    /* ==========================================================
     *  GLOBAL CART STATE
     * ========================================================== */
    let cart = {
        items: [],
        total: 0
    };
    let fletesPendientesGlobal = [];

    function formatFechaSV(fecha) {
        if (!fecha) return '—';

        const date = new Date(fecha);

        return new Intl.DateTimeFormat('es-SV', {
            weekday: 'long',
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
            timeZone: 'America/El_Salvador'
        }).format(date);
    }

    function renderPackages(packages) {
        const container = document.getElementById('packages-container');
        container.innerHTML = '';

        if (!packages.length) {
            container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-info">
                    No hay paquetes pendientes para este vendedor.
                </div>
            </div>
        `;
            return;
        }

        packages
            .filter(pkg => parseFloat(pkg.monto || 0) > 0)
            .forEach(pkg => {

                const monto = parseFloat(pkg.monto || 0);
                const pendiente = parseFloat(pkg.flete_pendiente || 0);
                const netAmount = monto - pendiente;

                const imageUrl = pkg.foto ?
                    `/upload/paquetes/${pkg.foto}` :
                    `/upload/no-image.png`;

                const col = document.createElement('div');
                col.className = currentView === 'grid' ?
                    'col-md-3 mb-3' :
                    'col-12 mb-2';
                if (currentView === 'grid') {
                    col.innerHTML = `
                    <div class="card package-card h-100 shadow-sm position-relative"
                        data-package-id="${pkg.id}">
                        <div class="package-image-wrapper">
                            <img 
                                src="/upload/placeholder.png"
                                data-src="${imageUrl}"
                                class="card-img-top lazy-img"
                                loading="lazy"
                                alt="Paquete ${pkg.id}">
                        </div>
                        <div class="card-body">
                            <h6 class="mb-1">Paquete #${pkg.id}</h6>
                            <h6 class="mb-1">Cliente: ${pkg.cliente}</h6>
                            <h6 class="mb-1">
                                Fecha actualizada de entrega: ${formatFechaSV(pkg.updated_at)}
                            </h6>


                            <hr>

                            <div>Valor Paq: $</div>
                            <div class="package-amount fw-bold">
                                ${monto.toFixed(2)}
                            </div>

                            ${
                                pendiente > 0
                                ? `<div class="text-danger small">
                                    Descuento pendiente: -$${pendiente.toFixed(2)}
                                </div>`
                                : ''
                            }
                        </div>

                        <div class="package-footer d-flex justify-content-between align-items-center px-3 py-2 bg-light">
                            <strong>Total a pagar:</strong>
                            <span class="fw-bold text-success">
                                $${netAmount.toFixed(2)}
                            </span>
                        </div>

                        <div class="p-2">
                            <button type="button" class="btn btn-outline-success w-100 btn-add">
                                Agregar al pago
                            </button>
                    </div>
                `;
                } else {
                    col.innerHTML = `
                    <div class="card shadow-sm p-3 d-flex flex-row justify-content-between position-relative"
                        data-package-id="${pkg.id}">
                        <div>
                            <strong>PK-${pkg.id}</strong><br>
                            <small>${pkg.cliente}</small><br>
                            <small>$${monto.toFixed(2)}</small>
                        </div>
                        <button type="button" class="btn btn-success btn-sm btn-add">
                            Agregar
                        </button>
                    </div>
                `;
                }

                col.querySelector('.btn-add').addEventListener('click', (e) => {

                    addToCart({
                        id: pkg.id,
                        code: `PK-${pkg.id}`,
                        amount: netAmount,
                        photo: pkg.foto ?
                            `/upload/paquetes/${pkg.foto}` : `/upload/no-image.png`
                    });

                    // Efecto glow
                    const card = e.target.closest('.card');
                    card.classList.add('package-added');

                    setTimeout(() => {
                        card.classList.remove('package-added');
                    }, 600);

                    // Iluminar carrito
                    const cartBox = document.getElementById('payment-cart');
                    cartBox.classList.add('cart-glow');

                    setTimeout(() => {
                        cartBox.classList.remove('cart-glow');
                    }, 500);
                });

                container.appendChild(col);
            });
        syncSelectedPackagesVisual();
    }

    function syncSelectedPackagesVisual() {

        cart.items.forEach(item => {

            const cleanId = item.id.toString().replace('flete-', '');

            const card = document.querySelector(`[data-package-id="${cleanId}"]`);

            if (card) {

                card.classList.add('package-selected');

                const btn = card.querySelector('.btn-add');

                if (btn) {
                    btn.innerText = 'Agregado';
                    btn.classList.remove('btn-outline-success');
                    btn.classList.add('btn-success');
                }
            }
        });
    }

    /* ==========================================================
     *  LOAD PACKAGES BY SELLER
     * ========================================================== */
    $('#seller_id').on('change', function() {

        const sellerId = $(this).val();
        document.getElementById('packages-container').innerHTML = '';

        if (!sellerId) return;

        /* ===============================
           1️⃣ CARGAR PAQUETES NORMALES
        =============================== */

        fetch(`<?= site_url('payments/packages-by-seller') ?>/${sellerId}`)
            .then(res => res.json())
            .then(data => {
                renderPackages(data);
                initLazyLoad();
            })
            .catch(() => {
                Swal.fire('Error', 'No se pudieron cargar los paquetes', 'error');
            });

        /* ===============================
           2️⃣ CARGAR FLETES PENDIENTES
        =============================== */

        fetch(`<?= site_url('payments/fletes-pendientes') ?>/${sellerId}`)
            .then(res => res.json())
            .then(fletes => {

                if (!fletes.length) return;

                fletesPendientesGlobal = fletes;

                // Mostrar botón
                document.getElementById('btnVerFletes')
                    .classList.remove('d-none');

                // 🔥 Sweet automático
                Swal.fire({
                    title: 'Fletes pendientes detectados',
                    html: `
                Se encontraron <strong>${fletes.length}</strong> 
                paquete(s) con flete pendiente.<br><br>
                ¿Desea revisarlos ahora?
            `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, revisarlos',
                    cancelButtonText: 'Después'
                }).then(result => {

                    if (!result.isConfirmed) return;

                    renderFletesModal();
                    $('#modalFletes').modal('show');
                    activarSeleccionVisualFletes();
                });

            });

    });

    function renderFletesModal() {

        const container = document.getElementById('fletes-container');
        container.innerHTML = '';

        fletesPendientesGlobal.forEach(pkg => {
            const estado = pkg.estatus || 'Sin estado';

            let badgeClass = 'badge-info';

            if (estado === 'no_retirado') {
                badgeClass = 'badge-danger';
            }

            const yaEnCarrito = cart.items.some(i => i.id === 'flete-' + pkg.id);

            const imageUrl = pkg.foto ?
                `/upload/paquetes/${pkg.foto}` :
                `/upload/no-image.png`;

            const monto = parseFloat(pkg.monto || 0);
            const fletePendiente = parseFloat(pkg.flete_pendiente || 0);

            const col = document.createElement('div');
            col.className = 'col-md-4 mb-3';

            col.innerHTML = `
        <div class="card shadow-sm h-100 border-warning package-card package-selectable">

            <div style="
                height:180px;
                overflow:hidden;
                border-top-left-radius:.25rem;
                border-top-right-radius:.25rem;
            ">
                <img src="${imageUrl}"
                    style="width:100%; height:100%; object-fit:cover;">
            </div>

            <div class="card-body position-relative">

                <div class="form-check position-absolute"
                    style="top:10px; right:10px;">
                    <input class="form-check-input chk-flete-card"
                        type="checkbox"
                        value="${pkg.id}"
                        data-monto="${fletePendiente}"
                        ${yaEnCarrito ? 'checked' : ''}>
                </div>

                <h6 class="mb-1">Paquete #${pkg.id}</h6>
                <p class="mb-1 text-muted">${pkg.cliente}</p>

                <div class="mb-2">
                    <span class="badge ${badgeClass}">
                        ${estado}
                    </span>
                </div>

                <div class="small text-muted">
                    Total del paquete:
                </div>
                <div class="font-weight-bold">
                    $${monto.toFixed(2)}
                </div>

                <div class="text-danger font-weight-bold mt-2">
                    Flete pendiente: $${fletePendiente.toFixed(2)}
                </div>

            </div>
        </div>
        `;

            container.appendChild(col);

            if (yaEnCarrito) {
                col.querySelector('.package-selectable').classList.add('selected');
            }
        });
    }

    function activarSeleccionVisualFletes() {

        document.querySelectorAll('#modalFletes .package-selectable')
            .forEach(card => {

                const checkbox = card.querySelector('.chk-flete-card');

                // Click en toda la card
                card.addEventListener('click', function(e) {

                    // evitar doble toggle si clickea directo el checkbox
                    if (e.target.type !== 'checkbox') {
                        checkbox.checked = !checkbox.checked;
                    }

                    card.classList.toggle('selected', checkbox.checked);
                });

                // Cambio directo del checkbox
                checkbox.addEventListener('change', function() {
                    card.classList.toggle('selected', this.checked);
                });

            });
    }
    // Mostrar paquetes con solo flete pendiente
    function mostrarFletesPendientes(fletes) {

        Swal.fire({
            title: 'Fletes pendientes detectados',
            text: `Este vendedor tiene ${fletes.length} paquete(s) con flete pendiente.`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Ver paquetes'
        }).then(result => {

            if (!result.isConfirmed) return;

            let html = '<div class="text-start">';

            fletes.forEach(pkg => {
                html += `
                <div class="form-check mb-2">
                    <input class="form-check-input chk-flete" 
                           type="checkbox" 
                           value="${pkg.id}"
                           data-monto="${pkg.flete_pendiente}">
                    <label class="form-check-label">
                        Paquete #${pkg.id} — 
                        Flete: $${parseFloat(pkg.flete_pendiente).toFixed(2)}
                    </label>
                </div>
            `;
            });

            html += '</div>';

            Swal.fire({
                title: 'Seleccionar fletes a descontar',
                html: html,
                showCancelButton: true,
                confirmButtonText: 'Agregar al carrito',
                preConfirm: () => {

                    const seleccionados = [];

                    document.querySelectorAll('.chk-flete:checked')
                        .forEach(chk => {

                            seleccionados.push({
                                id: 'flete-' + chk.value,
                                code: `FLETE-PK-${chk.value}`,
                                amount: -parseFloat(chk.dataset.monto),
                                photo: '/upload/no-image.png',
                                type: 'flete'
                            });

                        });

                    return seleccionados;
                }
            }).then(result => {

                if (!result.isConfirmed) return;

                result.value.forEach(item => addToCart(item));
            });
        });
    }
    /* ==========================================================
     *  CART FUNCTIONS
     * ========================================================== */
    function renderCartItems() {
        const list = document.getElementById('cart-items');
        list.innerHTML = '';

        cart.items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'list-group-item p-2';

            li.innerHTML = `
            <div class="d-flex align-items-center">
                
                <!-- FOTO -->
                <div style="
                    width:45px;
                    height:45px;
                    flex-shrink:0;
                    border-radius:6px;
                    overflow:hidden;
                    border:1px solid #ddd;
                    margin-right:10px;
                ">
                    <img src="${item.photo}"
                         style="width:100%; height:100%; object-fit:cover;">
                </div>

                <!-- INFO -->
                <div class="flex-grow-1">
                    <strong>${item.code}</strong><br>
                    <small class="text-muted">
                        $${parseFloat(item.amount).toFixed(2)}
                    </small>
                </div>

                <!-- BOTÓN -->
                <button class="btn btn-sm btn-outline-danger ms-2">
                    &times;
                </button>
            </div>
        `;

            li.querySelector('button')
                .addEventListener('click', () => removeFromCart(item.id));

            list.appendChild(li);
        });
    }

    function recalcCart() {
        cart.total = cart.items.reduce((sum, i) => sum + parseFloat(i.amount), 0);

        document.querySelector('.cart-total').innerText =
            `$${cart.total.toFixed(2)}`;

        document.querySelector('.cart-count').innerText =
            cart.items.length;

        renderCartItems();
    }

    function removeFromCart(id) {

        cart.items = cart.items.filter(i => i.id !== id);
        recalcCart();

        const cleanId = id.toString().replace('flete-', '');
        const card = document.querySelector(`[data-package-id="${cleanId}"]`);

        if (!card) return;

        card.classList.remove('package-selected');

        const btn = card.querySelector('.btn-add');

        if (!btn) return;

        // Limpiar clases de estado seleccionado
        btn.classList.remove('btn-success', 'btn-outline-success');

        // Restaurar según vista actual
        if (currentView === 'grid') {
            btn.classList.add('btn-outline-success');
            btn.innerText = 'Agregar al pago';
        } else {
            btn.classList.add('btn-success');
            btn.innerText = 'Agregar';
        }
    }

    function addToCart(pkg) {

        if (cart.items.find(i => i.id === pkg.id)) return;

        cart.items.push(pkg);
        recalcCart();

        // 🔥 Aplicar efecto visual inmediato
        const cleanId = pkg.id.toString().replace('flete-', '');

        const card = document.querySelector(`[data-package-id="${cleanId}"]`);

        if (card) {

            card.classList.add('package-selected');

            const btn = card.querySelector('.btn-add');

            if (btn) {
                btn.innerText = 'Agregado';
                btn.classList.remove('btn-outline-success');
                btn.classList.add('btn-success');
            }
        }
    }

    function initLazyLoad() {
        const images = document.querySelectorAll('.lazy-img');

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-img');
                    obs.unobserve(img);
                }
            });
        });

        images.forEach(img => observer.observe(img));
    }

    // Cargar fletes pendientes al seleccionar vendedor
    function loadFletesPendientes(sellerId, abrirModal = false) {

        if (!sellerId) return;

        fetch(`<?= site_url('payments/fletes-pendientes') ?>/${sellerId}`)
            .then(res => res.json())
            .then(fletes => {

                fletesPendientesGlobal = fletes;

                if (!fletes.length) {
                    document.getElementById('fletes-container').innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-info">
                                No hay fletes pendientes.
                            </div>
                        </div>
                    `;
                    return;
                }

                renderFletesModal();
                activarSeleccionVisualFletes();

                if (abrirModal) {
                    $('#modalFletes').modal('show');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'No se pudieron cargar los fletes', 'error');
            });
    }

    /* ==========================================================
     *  DOM READY
     * ========================================================== */
    document.addEventListener('DOMContentLoaded', async () => {

        //switch de vista
        document.getElementById('viewGrid').addEventListener('click', () => {
            currentView = 'grid';
            localStorage.setItem('packageView', 'grid');
            $('#seller_id').trigger('change');
        });

        document.getElementById('viewList').addEventListener('click', () => {
            currentView = 'list';
            localStorage.setItem('packageView', 'list');
            $('#seller_id').trigger('change');
        });

        /* ------------------------------------------------------
         *  CART TOGGLE
         * ------------------------------------------------------ */
        document.getElementById('cartToggle')
            .addEventListener('click', () => {
                document.getElementById('payment-cart')
                    .classList.toggle('minimized');
            });

        /* ------------------------------------------------------
         *  SELECT ALL FLETES
         * ------------------------------------------------------ */
        document.getElementById('selectAllFletes')
            .addEventListener('click', () => {

                document.querySelectorAll('.chk-flete-card')
                    .forEach(chk => chk.checked = true);
            });

        /* ------------------------------------------------------
         *  MOSTRAR MODAL FLETES PENDIENTES
         * ------------------------------------------------------ */

        $('#btnVerFletes').on('click', function() {

            const sellerId = $('#seller_id').val();
            loadFletesPendientes(sellerId, true);

        });
        
        /*
        * Botón de seleccionar todos los fletes pendientes en el modal
        */
        const btnSelectAll = document.getElementById('selectAllFletes');

        if (btnSelectAll) {

            btnSelectAll.addEventListener('click', () => {

                const checkboxes = document.querySelectorAll('#modalFletes .chk-flete-card');

                if (!checkboxes.length) return;

                // Verificar si todos están seleccionados
                const todosSeleccionados = [...checkboxes].every(chk => chk.checked);

                checkboxes.forEach(chk => {

                    const card = chk.closest('.package-selectable');

                    if (todosSeleccionados) {
                        // 🔻 Deseleccionar todos
                        chk.checked = false;
                        card.classList.remove('selected');
                    } else {
                        // 🔺 Seleccionar todos
                        chk.checked = true;
                        card.classList.add('selected');
                    }

                });

            });

        }

        /* ------------------------------------------------------
         *  AGREGAR FLETES SELECCIONADOS
         * ------------------------------------------------------ */

        document.getElementById('agregarFletes')
            .addEventListener('click', () => {

                document.querySelectorAll('.chk-flete-card:checked')
                    .forEach(chk => {

                        addToCart({
                            id: 'flete-' + chk.value,
                            code: `FLETE-PK-${chk.value}`,
                            amount: -parseFloat(chk.dataset.monto),
                            photo: '/upload/no-image.png',
                            type: 'flete'
                        });

                    });

                $('#modalFletes').modal('hide');
            });


        /* ------------------------------------------------------
         *  PAY BUTTON
         * ------------------------------------------------------ */
        document.getElementById('btnPay')
            .addEventListener('click', () => {

                if (!$('#seller_id').val()) {
                    Swal.fire('Atención', 'Seleccione un vendedor', 'warning');
                    return;
                }

                if (cart.items.length === 0) {
                    Swal.fire('Atención', 'No hay paquetes seleccionados', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Confirmar pago',
                    html: `
                    <strong>Total:</strong> $${cart.total.toFixed(2)}<br>
                    <strong>Paquetes:</strong> ${cart.items.length}
                `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Pagar'
                }).then(result => {

                    if (!result.isConfirmed) return;

                    const cuentaAsignada = $('.select2-account').val();

                    fetch('<?= site_url("payments/pay-seller-byaccount") ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                seller_id: $('#seller_id').val(),
                                cuenta_id: cuentaAsignada,
                                packages: cart.items
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {

                                Swal.fire('Pago realizado', '', 'success');

                                // Limpiar carrito
                                cart = {
                                    items: [],
                                    total: 0
                                };
                                recalcCart();

                                // Recargar paquetes del vendedor actual
                                const sellerId = $('#seller_id').val();

                                fetch(`<?= site_url('payments/packages-by-seller') ?>/${sellerId}`)
                                    .then(res => res.json())
                                    .then(packages => {
                                        renderPackages(packages);
                                    });
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        });
                });
            });


    });
</script>
<script>
    $(document).ready(function() {

        // Inicializar Select2 para selección de cuentas
        $('.select2-account').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Buscar cuenta...',
            allowClear: true,
            minimumInputLength: 1,
            language: {
                inputTooShort: function() {
                    return 'Ingrese 1 o más caracteres';
                }
            },
            ajax: {
                url: "<?= base_url('accounts-list') ?>",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.name
                        }))
                    };
                }
            }
        });

        // 🟢 Obtener desde el servidor la cuenta con ID 1
        $.ajax({
            url: "<?= base_url('accounts-list') ?>",
            data: {
                q: "efectivo"
            }, // cualquier valor, el backend lo ignora si devuelves siempre la lista
            dataType: "json",
            success: function(data) {

                // buscar cuenta ID = 1
                const cuenta = data.find(item => item.id == 1);

                if (!cuenta) return; // si no existe, no ponemos nada

                // Colocar como selección inicial en todos los select2
                $('.select2-account').each(function() {
                    let option = new Option(cuenta.name, cuenta.id, true, true);
                    $(this).append(option).trigger('change');
                });
            }
        });

    });
</script>

<?= $this->endSection() ?>