<?php
session_start();
require 'conexion.php'; 

$id_insumo = '';
$nombre = '';
$stock_actual = '';
$stock_minimo = '';
$unidad_medida = '';
$estado = '';

$alerta_mensaje = "";
$alerta_tipo = "info"; 
$accion = $_POST['accion'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($accion == 'limpiar') {
        $alerta_mensaje = "Formulario limpiado.";
        $alerta_tipo = "info";
    } 
    else {
        $id_insumo = $_POST['id_insumo'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $stock_actual = $_POST['stock_actual'] ?? '';
        $stock_minimo = $_POST['stock_minimo'] ?? '';
        $unidad_medida = $_POST['unidad_medida'] ?? '';
        $estado = $_POST['estado'] ?? '';

        if ($accion == 'eliminar') {
            if (!empty($id_insumo)) {
                $id_escapado = mysqli_real_escape_string($con, $id_insumo);
                
                $sql_delete = "DELETE FROM insumo WHERE id_insumo = '$id_escapado'";
                
                if (mysqli_query($con, $sql_delete)) {
                    $alerta_mensaje = "🗑️ Insumo eliminado exitosamente.";
                    $alerta_tipo = "success";
                    $id_insumo = $nombre = $stock_actual = $stock_minimo = $unidad_medida = $estado = '';
                } else {
                    $alerta_mensaje = "❌ ERROR al eliminar: " . mysqli_error($con);
                    $alerta_tipo = "error";
                }
            } else {
                $alerta_mensaje = "ERROR: Debe buscar un insumo para poder eliminarlo.";
                $alerta_tipo = "error";
            }
        }
        elseif ($accion == 'guardar') {
            if (empty($nombre) || empty($stock_actual) || empty($estado)) {
                $alerta_mensaje = "ERROR: Nombre, Stock Actual y Estado son obligatorios.";
                $alerta_tipo = "error";
            } else {
                $nombre_e = mysqli_real_escape_string($con, $nombre);
                $stock_a_e = mysqli_real_escape_string($con, $stock_actual);
                $stock_m_e = mysqli_real_escape_string($con, $stock_minimo);
                $unidad_e = mysqli_real_escape_string($con, $unidad_medida);
                $estado_e = mysqli_real_escape_string($con, $estado);

                $sql_insert = "INSERT INTO insumo (nombre, stock_actual, stock_minimo, unidad_medida, estado) 
                               VALUES ('$nombre_e', '$stock_a_e', '$stock_m_e', '$unidad_e', '$estado_e')";
                
                if (mysqli_query($con, $sql_insert)) {
                    $alerta_mensaje = "✅ Insumo registrado exitosamente.";
                    $alerta_tipo = "success";
                    $id_insumo = $nombre = $stock_actual = $stock_minimo = $unidad_medida = $estado = '';
                } else {
                    $alerta_mensaje = "❌ ERROR al guardar: " . mysqli_error($con);
                    $alerta_tipo = "error";
                }
            }
        }
        elseif ($accion == 'modificar') {
            if (empty($id_insumo) || empty($nombre)) {
                $alerta_mensaje = "ERROR: ID y Nombre son obligatorios para modificar.";
                $alerta_tipo = "error";
            } else {
                $id_escapado = mysqli_real_escape_string($con, $id_insumo);
                $nombre_e = mysqli_real_escape_string($con, $nombre);
                $stock_a_e = mysqli_real_escape_string($con, $stock_actual);
                $stock_m_e = mysqli_real_escape_string($con, $stock_minimo);
                $unidad_e = mysqli_real_escape_string($con, $unidad_medida);
                $estado_e = mysqli_real_escape_string($con, $estado);

                $sql_update = "UPDATE insumo SET 
                               nombre = '$nombre_e', 
                               stock_actual = '$stock_a_e', 
                               stock_minimo = '$stock_m_e', 
                               unidad_medida = '$unidad_e', 
                               estado = '$estado_e' 
                               WHERE id_insumo = '$id_escapado'";
                
                if (mysqli_query($con, $sql_update)) {
                    $alerta_mensaje = "✅ Insumo modificado exitosamente.";
                    $alerta_tipo = "success";
                    $id_insumo = $nombre = $stock_actual = $stock_minimo = $unidad_medida = $estado = '';
                } else {
                    $alerta_mensaje = "❌ ERROR al modificar: " . mysqli_error($con);
                    $alerta_tipo = "error";
                }
            }
        }
        elseif ($accion == 'buscar') {
            if (!empty($nombre)) {
                $nombre_e = mysqli_real_escape_string($con, $nombre);
                $sql_buscar = "SELECT * FROM insumo WHERE nombre LIKE '%$nombre_e%' LIMIT 1";
                $res = mysqli_query($con, $sql_buscar);
                
                if ($res && mysqli_num_rows($res) > 0) {
                    $fila = mysqli_fetch_assoc($res);
                    $id_insumo = $fila['id_insumo'];
                    $nombre = $fila['nombre'];
                    $stock_actual = $fila['stock_actual'];
                    $stock_minimo = $fila['stock_minimo'];
                    $unidad_medida = $fila['unidad_medida'];
                    $estado = $fila['estado'];
                    
                    $alerta_mensaje = "Insumo encontrado. ID: $id_insumo";
                    $alerta_tipo = "info";
                } else {
                    $alerta_mensaje = "No se encontró ningún insumo con ese nombre.";
                    $alerta_tipo = "warning";
                    $id_insumo = '';
                }
            } else {
                $alerta_mensaje = "Ingrese un nombre para buscar.";
                $alerta_tipo = "info";
            }
        }
    }
}

$sql_tabla = "SELECT * FROM insumo ORDER BY id_insumo ASC";
$datos = mysqli_query($con, $sql_tabla);

$id_html = $id_insumo;
$nom_html = $nombre;
$stk_a_html = $stock_actual;
$stk_m_html = $stock_minimo;
$uni_html = $unidad_medida;
$est_html = $estado;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Gestion de Insumos</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="imagenes/DC_Logo_Cabecera.png">
        <link rel="stylesheet" href="CSS/formInsumo.css"/>
        <style>
            .alerta-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .tabla-insumos tbody tr { cursor: default; }
        </style>
    </head>
    <body>
        <main>
            <h1>CEVICHERIA DON CANGREJO</h1>
            <h2>Gestión de Insumos</h2>
            
            <?php if (!empty($alerta_mensaje)): ?>
                <div class="alerta-<?= $alerta_tipo ?>"><?= $alerta_mensaje ?></div>
            <?php endif; ?>

            <form id="formInsumo" action="formInsumo.php" method="post">
                <input type="hidden" id="id_insumo" name="id_insumo" value="<?= htmlspecialchars($id_html) ?>"/>
                
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($nom_html) ?>" required/>
                </div>
                <div class="form-group">
                    <label for="stock_actual">Stock Actual</label>
                    <input type="text" id="stock_actual" name="stock_actual" value="<?= htmlspecialchars($stk_a_html) ?>"/>
                </div>
                <div class="form-group">
                    <label>Stock Minimo</label>
                    <input type="text" id="stock_minimo" name="stock_minimo" value="<?= htmlspecialchars($stk_m_html) ?>"/>
                </div>
                <div class="form-group">
                    <label for="unidad_medida">Unidad de Medida</label>
                    <input type="text" id="unidad_medida" name="unidad_medida" value="<?= htmlspecialchars($uni_html) ?>"/>
                </div>
                <div>
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="" disabled <?= empty($est_html) ? 'selected' : '' ?>> Seleccione el estado</option>
                        <option value="DISPONIBLE" <?= $est_html == 'DISPONIBLE' ? 'selected' : '' ?>>DISPONIBLE</option>
                        <option value="AGOTADO" <?= $est_html == 'AGOTADO' ? 'selected' : '' ?>>AGOTADO</option>
                    </select>
                </div>
                
                <div class="grupo-botones">
                    <button type="submit" name="accion" value="buscar" class="btn btn-buscar">Buscar</button>
                    
                    <button type="submit" name="accion" value="guardar" class="btn btn-guardar" 
                        <?= !empty($id_html) ? 'disabled' : '' ?>>Guardar Nuevo</button>
                        
                    <button type="submit" name="accion" value="modificar" class="btn btn-modificar" 
                        <?= empty($id_html) ? 'disabled' : '' ?>>Modificar</button>
                        
                    <button type="submit" name="accion" value="eliminar" class="btn btn-eliminar" 
                        <?= empty($id_html) ? 'disabled' : '' ?>>Eliminar</button>
                    
                    <button type="submit" name="accion" value="limpiar" class="btn btn-limpiar">Limpiar</button>
                </div>
            </form>
            
            <div class="tabla-insumos-container">
                <h2>Listado de Insumos</h2>
                <div class="table-responsive">
                    <table class="tabla-insumos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Stock Actual</th>
                                <th>Stock Minimo</th>
                                <th>Unidad de Medida</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($datos && mysqli_num_rows($datos) > 0) {
                                while($row = mysqli_fetch_array($datos, MYSQLI_ASSOC)) {
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['id_insumo']) ?></td>
                                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                                        <td><?= htmlspecialchars($row['stock_actual']) ?></td>
                                        <td><?= htmlspecialchars($row['stock_minimo']) ?></td>
                                        <td><?= htmlspecialchars($row['unidad_medida']) ?></td>
                                        <td><?= htmlspecialchars($row['estado']) ?></td>
                                    </tr>
                                    <?php
                                }
                                mysqli_free_result($datos);
                            } else {
                                ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No se encontraron insumos registrados.</td>
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