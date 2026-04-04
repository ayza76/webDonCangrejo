<?php
session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion'])) {
    if ($_POST['accion'] == 'cambiar_estado') {
        $id_pedido_act = $_POST['id_pedido'];
        $nuevo_estado = $_POST['nuevo_estado'];
        
        $sql_update = "UPDATE pedido SET estado = '$nuevo_estado' WHERE id_pedido = '$id_pedido_act'";
        mysqli_query($con, $sql_update);
    }
}

$segundos_refresh = 15;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="<?= $segundos_refresh ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor de Cocina / Pedidos</title>
    <link rel="icon" href="imagenes/DC_Logo_Cabecera.png">
    
    <link rel="stylesheet" href="CSS/alertaPedidos.css"> 
    <link rel="stylesheet" href="CSS/formInsumo.css"/>
</head>
<body>

    <div class="header-top">
        <div>
            <h1>🍳 Monitor de Cocina</h1>
            <small>Prioridad: Pendientes arriba (Actualización: <?= $segundos_refresh ?>s)</small>
        </div>
    </div>

    <div class="contenedor-tabla">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th style="width: 100px;">Hora</th>
                    <th style="width: 150px;">Tipo Pedido</th>
                    <th>Detalle del Pedido</th>
                    <th style="width: 200px;">Estado / Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT 
                            p.id_pedido, 
                            p.fecha_hora, 
                            p.tipo_pedido,      /* DATO CLAVE: Salon, Delivery, Para Llevar */
                            p.direccion_entrega, 
                            p.estado, 
                            c.nombre_completo,
                            m.numero_mesa,
                            GROUP_CONCAT(
                                CONCAT('• ', prod.nombre, ' <b>(x', dp.cantidad, ')</b>') 
                                SEPARATOR '<br>'
                            ) AS lista_productos
                        FROM pedido p
                        INNER JOIN cliente c ON p.id_cliente = c.id_cliente
                        INNER JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                        INNER JOIN producto prod ON dp.id_producto = prod.id_producto
                        LEFT JOIN mesa m ON p.id_mesa = m.id_mesa
                        GROUP BY p.id_pedido
                        ORDER BY 
                            -- Lógica: Pendientes (1) arriba, el resto (2) abajo
                            CASE WHEN p.estado = 'Pendiente' THEN 1 ELSE 2 END ASC,
                            p.id_pedido ASC"; 

                $res = mysqli_query($con, $sql);

                if ($res && mysqli_num_rows($res) > 0) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        
                        $visual_tipo = "";
                        
                        if ($row['tipo_pedido'] == 'Salon') {
                            $num_mesa = !empty($row['numero_mesa']) ? $row['numero_mesa'] : "?";
                            $visual_tipo = "<div style='text-align:center'><span class='badge-mesa'>" . $num_mesa . "</span><br><small>En Salón</small></div>";
                        
                        } elseif ($row['tipo_pedido'] == 'Delivery') {
                            $dir = !empty($row['direccion_entrega']) ? $row['direccion_entrega'] : "Sin dirección";
                            $visual_tipo = "<span class='info-delivery'>🛵 Delivery</span><br><small>" . $dir . "</small>";
                        
                        } elseif ($row['tipo_pedido'] == 'Para Llevar') {
                            $visual_tipo = "<div style='text-align:center'><span class='badge-para-llevar'>🛍 Para Llevar</span></div>";
                        } else {
                            $visual_tipo = $row['tipo_pedido']; 
                        }

                        $clase_fila = "fila-" . $row['estado'];
                        ?>
                        
                        <tr class="<?= $clase_fila ?>">
                            <td style="text-align:center; font-weight:bold; font-size: 1.1em;">#<?= $row['id_pedido'] ?></td>
                            <td style="text-align:center;"><?= date("H:i", strtotime($row['fecha_hora'])) ?></td>
                            
                            <td><?= $visual_tipo ?></td>
                            
                            <td class="col-detalle">
                                <strong>Cliente: <?= htmlspecialchars($row['nombre_completo']) ?></strong>
                                <hr style="margin: 5px 0; border: 0; border-top: 1px dashed #ccc;">
                                <?= $row['lista_productos'] ?>
                            </td>

                            <td>
                                <form method="post" class="form-estado">
                                    <input type="hidden" name="accion" value="cambiar_estado">
                                    <input type="hidden" name="id_pedido" value="<?= $row['id_pedido'] ?>">
                                    
                                    <select name="nuevo_estado" class="select-estado">
                                        <option value="Pendiente" <?= $row['estado'] == 'Pendiente' ? 'selected' : '' ?>>⏳ Pendiente</option>
                                        <option value="Entregado" <?= $row['estado'] == 'Entregado' ? 'selected' : '' ?>>✅ Entregado</option>
                                        <option value="Pagado" <?= $row['estado'] == 'Pagado' ? 'selected' : '' ?>>💰 Pagado</option>
                                        <option value="Anulado" <?= $row['estado'] == 'Anulado' ? 'selected' : '' ?>>🚫 Anulado</option>
                                    </select>

                                    <button type="submit" class="btn-check" title="Guardar Cambio">💾</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 30px; color: #666; font-size: 1.1em;'>No hay pedidos registrados en este momento.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>