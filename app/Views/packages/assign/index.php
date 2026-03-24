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

    .swal2-container {
        z-index: 10000 !important;
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

    .item-paquete:active {
        transform: scale(0.98);
    }

    .remove-btn {
        color: #dc3545;
        font-size: 18px;
        cursor: pointer;
        margin-top: 4px;
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
        overflow: hidden;
    }

    #checkOverlay {
        display: none;
    }

    .lista-paquetes {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .item-paquete {
        background: #fff;
        border-radius: 12px;
        padding: 10px 12px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .item-left {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .item-cliente {
        font-weight: 600;
        font-size: 14px;
    }

    .item-destino {
        font-size: 12px;
        color: #6c757d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 160px;
    }

    .item-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .item-valor {
        font-weight: bold;
        font-size: 14px;
    }

    .resumen-paquetes {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .resumen-item {
        flex: 1;
        background: #fff;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
    }

    .resumen-item small {
        display: block;
        font-size: 11px;
        color: #6c757d;
    }

    .resumen-item strong {
        font-size: 16px;
    }

    .estado {
        font-size: 11px;
        font-weight: 600;
    }

    .estado.ruta {
        color: #28a745;
    }

    .estado.casillero {
        color: #007bff;
    }

    #reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
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
                        Cerrar ✕
                    </button>
                </div>
                <div class="resumen-paquetes mb-2">
                    <div class="resumen-item">
                        <small>Total paquetes</small>
                        <strong id="totalPaquetes">0</strong>
                    </div>
                    <div class="resumen-item">
                        <small>Total $</small>
                        <strong id="totalDinero">$0.00</strong>
                    </div>
                </div>
                <div id="listaPaquetes" class="lista-paquetes"></div>

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

                <label>Flete total</label>
                <input type="number" id="fleteTotal" class="form-control mb-2" step="0.01">

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
                    width: 300,
                    height: 400
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
                                toast: true,
                                position: 'top',
                                icon: 'warning',
                                title: 'Ya agregado',
                                showConfirmButton: false,
                                timer: 1000
                            });

                            return;
                        }

                        paquetes.push({
                            id: p.id,
                            codigoqr: p.codigoqr,
                            cliente: p.cliente_nombre,
                            destino: p.destino,
                            valor: parseFloat(p.total || 0),
                            estado: 'ruta' 
                        });

                        render();

                        // ALERTA DE ÉXITO
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Agregado',
                            showConfirmButton: false,
                            timer: 500,
                            timerProgressBar: false
                        });

                        // animación visual
                        animacionCheck();

                    })
                    .finally(() => {
                        setTimeout(() => {
                            scanning = true;
                        }, 650);
                    });
            }
        );
    });
    const emptyState = document.getElementById('emptyState');
    const lista = document.getElementById('listaPaquetes');

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


    // RENDER
    function render() {

        lista.innerHTML = '';

        if (paquetes.length === 0) {
            emptyState.style.display = 'block';
            return;
        }

        emptyState.style.display = 'none';

        let total = 0;

        paquetes.forEach((p, i) => {

            total += p.valor;

            lista.innerHTML += `
        <div class="item-paquete">
            
            <div class="item-left">
                <div class="item-cliente">${p.cliente}</div>
                <div class="item-destino">${p.destino}</div>
                <small class="estado ${p.estado}">
                    ${p.estado === 'ruta' ? 'En ruta' : 'En casillero'}
                </small>
            </div>

            <div class="item-right">
                <div class="item-valor">$${p.valor.toFixed(2)}</div>

                <div class="d-flex gap-2 mt-1">
                    <button class="btn btn-sm btn-light"
                        onclick="configurar(${i})">
                        ⚙
                    </button>

                    <span class="remove-btn" onclick="eliminar(${i})">×</span>
                </div>
            </div>

        </div>
        `;
        });

        // 🔥 actualizar resumen
        document.getElementById('totalPaquetes').innerText = paquetes.length;
        document.getElementById('totalDinero').innerText = '$' + total.toFixed(2);
    }

    function configurar(index) {

        Swal.fire({
            title: 'Estado del paquete',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'En ruta',
            denyButtonText: 'En casillero',
            cancelButtonText: 'Cancelar'
        }).then((result) => {

            if (result.isConfirmed) {
                paquetes[index].estado = 'ruta';
            } else if (result.isDenied) {
                paquetes[index].estado = 'casillero';
            } else {
                return;
            }

            render();
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
            flete_total: parseFloat(document.getElementById('fleteTotal').value || 0),
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
                    Swal.close();
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