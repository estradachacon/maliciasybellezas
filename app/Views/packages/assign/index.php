<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    body {
        background: #f5f6fa;
    }

    .card {
        border-radius: 15px;
        border: none;
    }

    .qr-input {
        font-size: 20px;
        padding: 14px;
        text-align: center;
    }

    .paquete-card {
        background: white;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .paquete-header {
        display: flex;
        justify-content: space-between;
        font-weight: bold;
    }

    .paquete-body {
        font-size: 14px;
        margin-top: 5px;
    }

    .remove-btn {
        color: #dc3545;
        font-size: 18px;
        cursor: pointer;
    }

    .sticky-bottom {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 10px;
        border-top: 1px solid #ddd;
    }

    .btn-full {
        width: 100%;
        padding: 14px;
        font-size: 16px;
    }

    #scannerContainer {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #000;
        z-index: 9999;
    }

    #reader {
        width: 100%;
        height: 100%;
    }

    #checkOverlay {
        display: flex;
    }
</style>

<div class="row">

    <!-- IZQUIERDA (principal) -->
    <div class="col-md-8">

        <!-- HEADER -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Depositar paquetes</h5>
            </div>

            <div class="card-body">

                <!-- QR INPUT -->
                <div class="mb-3">
                    <input type="text" id="inputQR" class="form-control qr-input"
                        placeholder="Escanea o escribe el código QR">
                </div>
                <button id="btnCamara" class="btn btn-dark mb-2 w-100">
                    Abrir cámara
                </button>

                <div id="scannerContainer" style="display:none;">
                    <div id="reader" style="width:100%;"></div>

                    <!-- overlay check -->
                    <div id="checkOverlay" style="
                            display:none;
                            position:absolute;
                            top:0; left:0;
                            width:100%; height:100%;
                            background:rgba(0,0,0,0.6);
                            justify-content:center;
                            align-items:center;
                            z-index:10;
                        ">
                        <canvas id="checkCanvas" width="150" height="150"></canvas>
                    </div>
                    <button id="btnCerrarCamara" style="
                        position:absolute;
                        top:15px;
                        right:15px;
                        z-index:20;
                        background:#dc3545;
                        color:white;
                        border:none;
                        padding:10px 15px;
                        border-radius:8px;
                    ">
                        ✕ Cerrar
                    </button>
                </div>
                <!-- TABLA -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="tablaPaquetes">
                        <thead>
                            <tr>
                                <th>QR</th>
                                <th>Cliente</th>
                                <th>Destino</th>
                                <th>Valor</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div id="emptyState" class="text-center text-muted mt-3">
                    No hay paquetes agregados
                </div>

            </div>
        </div>

    </div>

    <!-- DERECHA (configuración) -->
    <div class="col-md-4">

        <div class="card shadow-sm mb-3">
            <div class="card-body">

                <label>Encomendista</label>
                <input type="text" id="encomendista" class="form-control mb-2">

                <label>Flete total</label>
                <input type="number" id="fleteTotal" class="form-control mb-2" step="0.01">

                <label>Tipo</label>
                <select id="tipo" class="form-control mb-3">
                    <option value="en_transito">En tránsito</option>
                    <option value="en_casillero">En casillero</option>
                </select>

                <button id="btnProcesar" class="btn btn-success w-100">
                    Procesar paquetes
                </button>

            </div>
        </div>

    </div>

</div>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let paquetes = [];
    let html5QrCode;
    let scanning = false;

    document.getElementById('btnCamara').addEventListener('click', async () => {

        document.getElementById('scannerContainer').style.display = 'block';

        html5QrCode = new Html5Qrcode("reader");

        scanning = true;

        await html5QrCode.start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            },
            (decodedText) => {

                if (!scanning) return;

                scanning = false;

                fetch("<?= base_url('packages-assign/buscar') ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'codigoqr=' + encodeURIComponent(decodedText)
                    })
                    .then(res => res.json())
                    .then(res => {

                        if (res.status !== 'ok') {

                            // 🔥 AQUÍ VA EL BONUS
                            Swal.fire({
                                icon: 'error',
                                title: 'QR no válido',
                                text: 'No se encontró el paquete',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            return;
                        }

                        let p = res.data;

                        if (paquetes.some(x => x.id == p.id)) {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Duplicado',
                                text: 'Este paquete ya fue agregado',
                                timer: 1200,
                                showConfirmButton: false
                            });

                            return;
                        }

                        paquetes.push({
                            id: p.id,
                            codigoqr: p.codigoqr,
                            cliente: p.cliente_nombre,
                            destino: p.destino,
                            valor: parseFloat(p.total || 0)
                        });

                        renderTabla();

                        // ✅ solo si fue exitoso
                        animacionCheck();

                    })
                    .finally(() => {
                        setTimeout(() => {
                            scanning = true;
                        }, 1200);
                    });
            }
        );
    });
    const inputQR = document.getElementById('inputQR');
    const lista = document.getElementById('listaPaquetes');
    const emptyState = document.getElementById('emptyState');

    // AUTOFOCUS CONSTANTE
    setInterval(() => inputQR.focus(), 500);

    // ESCANEAR
    inputQR.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            let codigo = this.value.trim();
            if (!codigo) return;

            buscarPaquete(codigo);
            this.value = '';
        }
    });

    function animacionCheck() {

        const overlay = document.getElementById('checkOverlay');
        const canvas = document.getElementById('checkCanvas');
        const ctx = canvas.getContext('2d');

        overlay.style.display = 'flex';

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        let progress = 0;

        const anim = setInterval(() => {

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // círculo
            ctx.beginPath();
            ctx.arc(75, 75, 60, 0, Math.PI * 2 * progress);
            ctx.strokeStyle = '#28a745';
            ctx.lineWidth = 8;
            ctx.stroke();

            // check
            if (progress > 0.7) {
                ctx.beginPath();
                ctx.moveTo(45, 75);
                ctx.lineTo(70, 100);
                ctx.lineTo(105, 50);
                ctx.strokeStyle = '#28a745';
                ctx.lineWidth = 8;
                ctx.stroke();
            }

            progress += 0.1;

            if (progress >= 1) {
                clearInterval(anim);

                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 300);
            }

        }, 30);
    }

    function procesarQR(qr) {

        fetch("<?= base_url('packages-assign/buscar') ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'codigoqr=' + encodeURIComponent(qr)
            })
            .then(res => res.json())
            .then(res => {

                if (res.status !== 'ok') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.msg
                    });
                    return;
                }

                let p = res.data;

                if (paquetes.some(x => x.id == p.id)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicado',
                        text: 'Este paquete ya fue agregado',
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return;
                }

                paquetes.push({
                    id: p.id,
                    codigoqr: p.codigoqr,
                    cliente: p.cliente_nombre,
                    destino: p.destino,
                    valor: parseFloat(p.total || 0)
                });

                renderTabla();
            });
    }

    function buscarPaquete(qr) {

        fetch("<?= base_url('packages-assign/buscar') ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'codigoqr=' + encodeURIComponent(qr)
            })
            .then(res => res.json())
            .then(res => {

                if (res.status !== 'ok') {
                    vibrar();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.msg
                    });
                    return;
                }

                let p = res.data;

                if (paquetes.some(x => x.id == p.id)) {
                    alert('Duplicado');
                    return;
                }

                paquetes.push({
                    id: p.id,
                    codigoqr: p.codigoqr,
                    cliente: p.cliente_nombre,
                    destino: p.destino,
                    valor: parseFloat(p.total || 0)
                });

                render();
            });
    }

    // RENDER
    function render() {

        lista.innerHTML = '';

        if (paquetes.length === 0) {
            emptyState.style.display = 'block';
            return;
        }

        emptyState.style.display = 'none';

        paquetes.forEach((p, i) => {

            lista.innerHTML += `
            <div class="paquete-card">
                <div class="paquete-header">
                    <span>${p.codigoqr}</span>
                    <span class="remove-btn" onclick="eliminar(${i})">×</span>
                </div>

                <div class="paquete-body">
                    ${p.cliente}<br>
                    ${p.destino}<br>
                    <b>$${p.valor.toFixed(2)}</b>
                </div>
            </div>
        `;
        });
    }

    function eliminar(i) {
        paquetes.splice(i, 1);
        render();
    }
    document.getElementById('btnCerrarCamara').addEventListener('click', cerrarCamara);

    async function cerrarCamara() {
        try {
            if (html5QrCode) {
                await html5QrCode.stop();
                await html5QrCode.clear();
            }
        } catch (e) {
            console.error(e);
        }

        document.getElementById('scannerContainer').style.display = 'none';
    }
    // GUARDAR
    document.getElementById('btnProcesar').addEventListener('click', function() {

        if (paquetes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin paquetes',
                text: 'Agrega al menos uno'
            });
            return;
        }

        let data = {
            encomendista: document.getElementById('encomendista').value,
            flete_total: parseFloat(document.getElementById('fleteTotal').value || 0),
            tipo: document.getElementById('tipo').value,
            paquetes: paquetes
        };

        fetch("<?= base_url('packages-assign/guardar') ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Guardado',
                        text: 'Paquetes procesados'
                    }).then(() => location.reload());
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.msg
                    });
                }
            });

    });

    // feedback vibración
    function vibrar() {
        if (navigator.vibrate) navigator.vibrate(100);
    }
</script>

<?= $this->endSection() ?>