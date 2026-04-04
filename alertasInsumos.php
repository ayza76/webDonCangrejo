<?php
require 'conexion.php';

$sql_tabla = "SELECT * FROM insumo ORDER BY stock_actual ASC";
$datos = mysqli_query($con, $sql_tabla);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Alerta de Insumos</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="imagenes/DC_Logo_Cabecera.png">
        
        <link rel="stylesheet" href="CSS/formInsumo.css"/>
        
        <style>
            body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
            
            .header-top { 
                display: flex; justify-content: space-between; align-items: center; 
                max-width: 95%; margin: 20px auto; 
            }
            
            .btn-volver { 
                background: #333; color: white; padding: 10px 20px; 
                text-decoration: none; border-radius: 5px; font-weight: bold; 
            }

            .alerta-roja {
                background-color: #ffe6e6; 
                color: #cc0000;
                font-weight: bold;
            }
            
            .alerta-roja td { border-bottom: 1px solid #ffcccc; }

            .tabla-insumos-container {
                max-width: 95%;
                margin: 0 auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
        </style>
    </head>
    <body>
        
        <div class="header-top">
            <h1>⚠️ Reporte de Stock de Insumos</h1>
        </div>

        <main>
            <div class="tabla-insumos-container">
                <h2>Listado General</h2>
                <div class="table-responsive">
                    <table class="tabla-insumos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Unidad</th>
                                <th>Estado</th>
                                <th>Situación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($datos && mysqli_num_rows($datos) > 0) {
                                while($row = mysqli_fetch_array($datos, MYSQLI_ASSOC)) {
                                    
                                    // LÓGICA DE ALERTA
                                    $clase_fila = "";
                                    $mensaje_situacion = "STOCK ACEPTABLE";
                                    $icono = "✅";

                                    // Si el stock es menor o igual al mínimo
                                    if ($row['stock_actual'] <= $row['stock_minimo']) {
                                        $clase_fila = "alerta-roja";
                                        $mensaje_situacion = "BAJO STOCK";
                                        $icono = "🚨";
                                    }
                                    ?>
                                    <tr class="<?= $clase_fila ?>">
                                        <td><?= htmlspecialchars($row['id_insumo']) ?></td>
                                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                                        <td><?= htmlspecialchars($row['stock_actual']) ?></td>
                                        <td><?= htmlspecialchars($row['stock_minimo']) ?></td>
                                        <td><?= htmlspecialchars($row['unidad_medida']) ?></td>
                                        <td><?= htmlspecialchars($row['estado']) ?></td>
                                        <td><?= $icono . " " . $mensaje_situacion ?></td>
                                    </tr>
                                    <?php
                                }
                                mysqli_free_result($datos);
                            } else {
                                ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">No se encontraron insumos registrados.</td>
                                </tr>
                                <?php
                            }

                            if (isset($con)) {
                                mysqli_close($con); 
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </body>
</html>