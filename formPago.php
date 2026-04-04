<?php
session_start();
require 'conexion.php'; 

$alerta_mensaje = "";
$alerta_tipo = "info"; 
$accion = $_POST['accion'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($accion == 'guardar_pago') {
        $id_pedido = $_POST['id_pedido'] ?? '';
        $metodo_pago = $_POST['metodo_pago'] ?? '';
        
        if (empty($id_pedido) || empty($metodo_pago)) {
            $alerta_mensaje = "ERROR: Seleccione un pedido y un método de pago.";
            $alerta_tipo = "error";
        } else {
            // Obtener monto real de la BD por seguridad
            $id_esc = mysqli_real_escape_string($con, $id_pedido);
            $sql_check = "SELECT total FROM pedido WHERE id_pedido = '$id_esc'";
            $res_check = mysqli_query($con, $sql_check);
            $fila_pedido = mysqli_fetch_assoc($res_check);
            
            $monto_total = $fila_pedido['total'];
            $fecha_hora = date('Y-m-d H:i:s');

            $sql_insert = "INSERT INTO pago (id_pedido, metodo_pago, monto_total, fecha_hora) 
                           VALUES ('$id_esc', '$metodo_pago', '$monto_total', '$fecha_hora')";

            if (mysqli_query($con, $sql_insert)) {
                // Actualizar estado a PAGADO
                $sql_update = "UPDATE pedido SET estado = 'Pagado' WHERE id_pedido = '$id_esc'";
                mysqli_query($con, $sql_update);

                $alerta_mensaje = "✅ ¡Pago registrado exitosamente!";
                $alerta_tipo = "success";
            } else {
                $alerta_mensaje = "❌ Error al registrar: " . mysqli_error($con);
                $alerta_tipo = "error";
            }
        }
    }
}

$sql_pendientes = "SELECT 
                        p.id_pedido, 
                        c.nombre_completo, 
                        p.total, 
                        p.tipo_pedido, 
                        p.id_mesa,
                        GROUP_CONCAT(
                            CONCAT('• ', prod.nombre, ' (x', dp.cantidad, ')') 
                            SEPARATOR '<br>'
                        ) AS detalle_html
                   FROM pedido p 
                   INNER JOIN cliente c ON p.id_cliente = c.id_cliente 
                   INNER JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                   INNER JOIN producto prod ON dp.id_producto = prod.id_producto
                   WHERE p.estado != 'Pagado' AND p.estado != 'Anulado' 
                   GROUP BY p.id_pedido
                   ORDER BY p.id_pedido DESC";

$res_pendientes = mysqli_query($con, $sql_pendientes);

// 2. CARGAR HISTORIAL
$sql_historial = "SELECT pa.id_pago, pa.fecha_hora, pa.metodo_pago, pa.monto_total, c.nombre_completo, p.id_pedido 
                  FROM pago pa
                  INNER JOIN pedido p ON pa.id_pedido = p.id_pedido
                  INNER JOIN cliente c ON p.id_cliente = c.id_cliente
                  ORDER BY pa.fecha_hora DESC LIMIT 10";
$res_historial = mysqli_query($con, $sql_historial);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Caja - Registrar Pago</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imagenes/DC_Logo_Cabecera.png">
    <link rel="stylesheet" href="CSS/formInsumo.css"/>
    <link rel="stylesheet" href="CSS/formPago.css"/> 
</head>
<body>
    <main>
        <h1>CEVICHERIA DON CANGREJO</h1>
        <h2>Módulo de Caja</h2>

        <?php if (!empty($alerta_mensaje)): ?>
            <div class="<?= 'alerta-'.$alerta_tipo ?>"><?= $alerta_mensaje ?></div>
        <?php endif; ?>

        <div class="caja-container">
            
            <div class="panel-cobro">
                <h3>💰 Registrar Nuevo Pago</h3>
                <form action="formPago.php" method="post">
                    
                    <div class="form-group">
                        <label>Seleccionar Pedido (Pendiente):</label>
                        
                        <select name="id_pedido" id="combo_pedidos" required onchange="mostrarInformacion()">
                            <option value="" data-total="0.00" data-detalle="">-- Seleccione Pedido --</option>
                            <?php 
                            if ($res_pendientes) {
                                while($p = mysqli_fetch_assoc($res_pendientes)) {
                                    
                                    $info_ubicacion = ($p['tipo_pedido'] == 'Salon') ? "Mesa: " . $p['id_mesa'] : $p['tipo_pedido'];
                                    
                                    $detalle_seguro = htmlspecialchars($p['detalle_html'], ENT_QUOTES, 'UTF-8');
                                    
                                    echo "<option value='{$p['id_pedido']}' 
                                                  data-total='{$p['total']}' 
                                                  data-detalle='{$detalle_seguro}'>
                                            #{$p['id_pedido']} - {$p['nombre_completo']} ($info_ubicacion)
                                          </option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Detalle del Consumo:</label>
                        <div id="box_detalle" class="ticket-preview">
                            <span style="color:#999; font-style:italic;">Seleccione un pedido para ver el detalle...</span>
                        </div>
                    </div>

                    <div class="display-total">
                        <label>Total a Pagar:</label>
                        <p class="total-grande">S/ <span id="txt_monto">0.00</span></p>
                    </div>

                    <div class="form-group">
                        <label>Método de Pago:</label>
                        <select name="metodo_pago" required>
                            <option value="" disabled selected>-- Seleccione Método --</option>
                            <option value="Efectivo">💵 Efectivo</option>
                            <option value="Tarjeta">💳 Tarjeta Crédito/Débito</option>
                            <option value="Yape">📱 Yape</option>
                            <option value="Plin">📱 Plin</option>
                        </select>
                    </div>

                    <button type="submit" name="accion" value="guardar_pago" class="btn-cobrar">CONFIRMAR PAGO</button>
                </form>
            </div>

            <div class="panel-historial">
                <h3>📜 Últimos Comprobantes Emitidos</h3>
                <div class="table-responsive">
                    <table class="tabla-pagos">
                        <thead>
                            <tr>
                                <th>ID Pago</th>
                                <th>Fecha/Hora</th>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Método</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($res_historial && mysqli_num_rows($res_historial) > 0) {
                                while($h = mysqli_fetch_assoc($res_historial)) {
                                    ?>
                                    <tr>
                                        <td><?= $h['id_pago'] ?></td>
                                        <td><?= date("d/m H:i", strtotime($h['fecha_hora'])) ?></td>
                                        <td>#<?= $h['id_pedido'] ?></td>
                                        <td><?= htmlspecialchars($h['nombre_completo']) ?></td>
                                        <td><span class="metodo-tag"><?= $h['metodo_pago'] ?></span></td>
                                        <td style="font-weight:bold; color:#28a745;">S/ <?= number_format($h['monto_total'], 2) ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align:center'>No hay pagos registrados hoy.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        function mostrarInformacion() {
            const combo = document.getElementById("combo_pedidos");
            const boxDetalle = document.getElementById("box_detalle");
            const txtMonto = document.getElementById("txt_monto");

            const opcionSeleccionada = combo.options[combo.selectedIndex];

            const total = opcionSeleccionada.getAttribute("data-total");
            const detalle = opcionSeleccionada.getAttribute("data-detalle");

            if (total && detalle) {
                txtMonto.innerText = parseFloat(total).toFixed(2);
                boxDetalle.innerHTML = "<span class='ticket-titulo'>CONSUMO:</span>" + detalle;
            } else {
                txtMonto.innerText = "0.00";
                boxDetalle.innerHTML = "<span style='color:#999; font-style:italic;'>Seleccione un pedido para ver el detalle...</span>";
            }
        }
    </script>
</body>
</html>