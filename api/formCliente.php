<?php
session_start();
require 'conexion.php'; 

$id_cliente = '';
$nombre_completo = '';
$dni = '';
$direccion = '';
$telefono = '';
$correo = '';

$alerta_mensaje = "";
$alerta_tipo = "info"; 
$accion = $_POST['accion'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_cliente = $_POST['id_cliente'] ?? '';
    $nombre_completo = $_POST['apnom'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';

    if ($accion == 'eliminar') {
        
        if (!empty($id_cliente)) {
            $id_escapado = mysqli_real_escape_string($con, $id_cliente);
            $sql_delete = "DELETE FROM cliente WHERE id_cliente = '$id_escapado'";
            
            if (mysqli_query($con, $sql_delete)) {
                $alerta_mensaje = "🗑️ Cliente con ID {$id_cliente} eliminado exitosamente.";
                $alerta_tipo = "success";
                
                $id_cliente = $nombre_completo = $dni = $direccion = $telefono = $correo = '';
            } else {
                $alerta_mensaje = "❌ ERROR al eliminar el cliente: " . mysqli_error($con);
                $alerta_tipo = "error";
            }
        } else {
            $alerta_mensaje = "ERROR: Debe seleccionar un cliente de la tabla para poder eliminarlo.";
            $alerta_tipo = "error";
        }
    }
    elseif ($accion == 'guardar') {
        
        if (empty($nombre_completo)) {
            $alerta_mensaje = "ERROR: El campo Nombre y Apellido es obligatorio.";
            $alerta_tipo = "error";
        } else {
            $nombre_escapado = mysqli_real_escape_string($con, $nombre_completo);
            $dni_escapado = mysqli_real_escape_string($con, $dni);
            $direccion_escapada = mysqli_real_escape_string($con, $direccion);
            $telefono_escapado = mysqli_real_escape_string($con, $telefono);
            $correo_escapado = mysqli_real_escape_string($con, $correo);
            
            $sql_insert = "INSERT INTO cliente (nombre_completo, dni, direccion, telefono, correo) 
                           VALUES ('$nombre_escapado', '$dni_escapado', '$direccion_escapada', '$telefono_escapado', '$correo_escapado')";
            
            if (mysqli_query($con, $sql_insert)) {
                $alerta_mensaje = "✅ Cliente '{$nombre_completo}' registrado exitosamente.";
                $alerta_tipo = "success";
                
                $id_cliente = $nombre_completo = $dni = $direccion = $telefono = $correo = '';
            } else {
                $alerta_mensaje = "❌ ERROR al guardar el cliente: " . mysqli_error($con);
                $alerta_tipo = "error";
            }
        }
    } 
    elseif ($accion == 'modificar') {
        
        if (empty($id_cliente) || empty($nombre_completo)) {
            $alerta_mensaje = "ERROR: Para modificar, el ID y el Nombre son obligatorios.";
            $alerta_tipo = "error";
        } else {
            $id_escapado = mysqli_real_escape_string($con, $id_cliente);
            $nombre_escapado = mysqli_real_escape_string($con, $nombre_completo);
            $dni_escapado = mysqli_real_escape_string($con, $dni);
            $direccion_escapada = mysqli_real_escape_string($con, $direccion);
            $telefono_escapado = mysqli_real_escape_string($con, $telefono);
            $correo_escapado = mysqli_real_escape_string($con, $correo);
            
            $sql_update = "UPDATE cliente SET 
                           nombre_completo = '$nombre_escapado', 
                           dni = '$dni_escapado', 
                           direccion = '$direccion_escapada', 
                           telefono = '$telefono_escapado', 
                           correo = '$correo_escapado' 
                           WHERE id_cliente = '$id_escapado'";
            
            if (mysqli_query($con, $sql_update)) {
                $alerta_mensaje = "✅ Cliente con ID {$id_escapado} modificado exitosamente.";
                $alerta_tipo = "success";
                
                $id_cliente = $nombre_completo = $dni = $direccion = $telefono = $correo = '';
                
            } else {
                $alerta_mensaje = "❌ ERROR al modificar el cliente: " . mysqli_error($con);
                $alerta_tipo = "error";
            }
        }
    }
    elseif ($accion == 'buscar') {
        
        if (!empty($nombre_completo)) {
            $nombre_escapado = mysqli_real_escape_string($con, $nombre_completo);
            
            $sql_buscar = "SELECT id_cliente, nombre_completo, dni, direccion, telefono, correo 
                           FROM cliente 
                           WHERE nombre_completo LIKE '%$nombre_escapado%'";
            
            $resultado_busqueda = mysqli_query($con, $sql_buscar);
            
            if ($resultado_busqueda) {
                if (mysqli_num_rows($resultado_busqueda) == 1) {
                    $cliente_encontrado = mysqli_fetch_assoc($resultado_busqueda);
                    
                    $id_cliente = $cliente_encontrado['id_cliente'];
                    $nombre_completo = $cliente_encontrado['nombre_completo'];
                    $dni = $cliente_encontrado['dni'];
                    $direccion = $cliente_encontrado['direccion'];
                    $telefono = $cliente_encontrado['telefono'];
                    $correo = $cliente_encontrado['correo'];
                    
                    $alerta_mensaje = "Cliente encontrado con exito ID: {$id_cliente}.";
                    $alerta_tipo = "info";
                } else if (mysqli_num_rows($resultado_busqueda) > 1) {
                    $alerta_mensaje = "Múltiples clientes encontrados. Haga clic en la fila para cargar los datos.";
                    $alerta_tipo = "info";
                } else {
                    $alerta_mensaje = "No se encontró ningún cliente con ese nombre.";
                    $alerta_tipo = "warning";
                }
            } else {
                $alerta_mensaje = "Error al ejecutar la consulta: " . mysqli_error($con);
                $alerta_tipo = "error";
            }
        } else {
            $alerta_mensaje = "Mostrando listado completo. Ingrese un nombre para buscar.";
            $alerta_tipo = "info";
        }
    } 
}


$sql_tabla = "SELECT id_cliente, nombre_completo, dni, direccion, telefono, correo 
              FROM cliente 
              ORDER BY id_cliente ASC";

$datos = mysqli_query($con, $sql_tabla);

if (!$datos) {
    $alerta_mensaje = "Error al cargar el listado de clientes: " . mysqli_error($con);
    $alerta_tipo = "error";
}

$id_cliente_html = $id_cliente;
$apnom_html = $nombre_completo;
$dni_html = $dni;
$direccion_html = $direccion;
$telefono_html = $telefono;
$correo_html = $correo;

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Gestión de Clientes</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="imagenes/DC_Logo_Cabecera.png"/>
        <link rel="stylesheet" href="CSS/formCliente.css"/>
        <style>
            .alerta-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .tabla-cliente tbody tr {
                cursor: default;
                transition: background-color 0.2s;
            }
        </style>
    </head>
    <body>
        <main>
            <h1>CEVICHERIA DON CANGREJO</h1>
            <h2>Gestión de Clientes</h2>

            <?php if (!empty($alerta_mensaje)): ?>
                <div class="alerta-<?= $alerta_tipo ?>"><?= $alerta_mensaje ?></div>
            <?php endif; ?>
            
            <form id="formCliente" action="formCliente.php" method="post">
                
                <input type="hidden" id="id_cliente" name="id_cliente" value="<?= htmlspecialchars($id_cliente_html) ?>"/>
                
                <div class="form-group">
                    <label for="apnom">Nombre y Apellido</label>
                    <input type="text" id="apnom" name="apnom" value="<?= htmlspecialchars($apnom_html) ?>" required/>
                </div>
                <div class="form-group">
                    <label for="dni">DNI</label>
                    <input type="text" id="dni" name="dni" maxlength="8" value="<?= htmlspecialchars($dni_html) ?>"/>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($direccion_html) ?>"/>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" maxlength="9" value="<?= htmlspecialchars($telefono_html) ?>"/>
                </div>
                <div class="form-group">
                    <label for="correo">Correo</label>
                    <input type="text" id="correo" name="correo" value="<?= htmlspecialchars($correo_html) ?>"/>
                </div>
                
                <div class="grupo-botones">
                    <button type="submit" name="accion" value="buscar" class="btn btn-buscar">Buscar</button>
                    
                    <button type="submit" name="accion" value="guardar" class="btn btn-guardar" <?= !empty($id_cliente_html) ? 'disabled' : '' ?>>Guardar Nuevo</button>
                    <button type="submit" name="accion" value="modificar" class="btn btn-modificar" <?= empty($id_cliente_html) ? 'disabled' : '' ?>>Modificar</button>
                    <button type="submit" name="accion" value="eliminar" class="btn btn-eliminar" <?= empty($id_cliente_html) ? 'disabled' : '' ?>>Eliminar</button>
                    
                    <button type="button" onclick="window.location.href='formCliente.php'" class="btn btn-limpiar">Limpiar</button>
                </div>
            </form>
            
            <div class="tabla-cliente-container">
                <h2>Listado de Clientes</h2>
                <div class="table-responsive">
                    <table class="tabla-cliente">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombres y Apellidos</th>
                                <th>DNI</th>
                                <th>Direccion</th>
                                <th>Telefono</th>
                                <th>Correo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            if ($datos && mysqli_num_rows($datos) > 0) {
                                while($row = mysqli_fetch_array($datos, MYSQLI_ASSOC)) {
                                    
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['id_cliente']) ?></td>
                                        <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($row['dni']) ?></td>
                                        <td><?= htmlspecialchars($row['direccion']) ?></td>
                                        <td><?= htmlspecialchars($row['telefono']) ?></td>
                                        <td><?= htmlspecialchars($row['correo']) ?></td>
                                    </tr>
                                    <?php
                                }
                                mysqli_free_result($datos);
                            } else {
                                ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No se encontraron clientes registrados.</td>
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