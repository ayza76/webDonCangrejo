<?php
session_start();
require 'conexion.php'; 

$id_cliente = '';
$nombre_completo = '';
$dni = '';
$direccion = '';
$telefono = '';
$correo = '';
$usuario_cred = '';
$contrasena = '';
$conf_contrasena = '';

$alerta_mensaje = "";
$alerta_tipo = "info"; 
$accion = $_POST['accion'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($accion == 'limpiar') {
        $alerta_mensaje = "Formulario limpiado.";
        $alerta_tipo = "info";
    } 
    else {
        $id_cliente = $_POST['id_cliente'] ?? '';
        $nombre_completo = $_POST['apnom'] ?? '';
        $dni = $_POST['dni'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $usuario_cred = $_POST['usuario'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';
        $conf_contrasena = $_POST['conf_contrasena'] ?? '';

        if ($accion == 'eliminar') {
            if (!empty($id_cliente)) {
                $id_escapado = mysqli_real_escape_string($con, $id_cliente);
                
                $sql_delete_cred = "DELETE FROM cliente_usuario WHERE id_cliente = '$id_escapado'";
                
                if (mysqli_query($con, $sql_delete_cred)) {
                    $alerta_mensaje = "🗑️ Credenciales del Cliente ID {$id_cliente} eliminadas exitosamente.";
                    $alerta_tipo = "success";
                    
                    $id_cliente = $nombre_completo = $dni = $direccion = $telefono = $correo = $usuario_cred = $contrasena = $conf_contrasena = '';
                } else {
                    $alerta_mensaje = "❌ ERROR al eliminar: " . mysqli_error($con);
                    $alerta_tipo = "error";
                }
            } else {
                $alerta_mensaje = "ERROR: Debe cargar un cliente para poder eliminar.";
                $alerta_tipo = "error";
            }
        }
        elseif ($accion == 'guardar') {
            
            if (!empty($id_cliente)) {
                
                if (empty($usuario_cred) || empty($contrasena)) {
                    $alerta_mensaje = "ERROR: Usuario y Contraseña son obligatorios.";
                    $alerta_tipo = "error";
                } elseif ($contrasena !== $conf_contrasena) {
                     $alerta_mensaje = "ERROR: Las contraseñas no coinciden.";
                     $alerta_tipo = "error";
                } else {
                    $id_escapado = mysqli_real_escape_string($con, $id_cliente);
                    $usuario_escapado = mysqli_real_escape_string($con, $usuario_cred);
                    $contrasena_escapada = mysqli_real_escape_string($con, $contrasena);
                    
                    $sql_insert_cred = "INSERT INTO cliente_usuario (id_cliente, usuario, contrasena) 
                                        VALUES ('$id_escapado', '$usuario_escapado', '$contrasena_escapada')";
                    
                    if (mysqli_query($con, $sql_insert_cred)) {
                        $alerta_mensaje = "✅ Credenciales guardadas exitosamente para el Cliente ID: {$id_cliente}";
                        $alerta_tipo = "success";
                        
                        $id_cliente = $nombre_completo = $dni = $direccion = $telefono = $correo = $usuario_cred = $contrasena = $conf_contrasena = '';
                    } else {
                        $alerta_mensaje = "❌ ERROR: Posiblemente este cliente ya tiene usuario. " . mysqli_error($con);
                        $alerta_tipo = "error";
                    }
                }
            } else {
                $alerta_mensaje = "ERROR: Primero debe BUSCAR un cliente para obtener su ID.";
                $alerta_tipo = "warning";
            }
        }
        elseif ($accion == 'modificar') {
            
            if (empty($id_cliente) || empty($usuario_cred)) {
                $alerta_mensaje = "ERROR: ID y Usuario son obligatorios.";
                $alerta_tipo = "error";
            } elseif (!empty($contrasena) && $contrasena !== $conf_contrasena) {
                 $alerta_mensaje = "ERROR: Las nuevas contraseñas no coinciden.";
                 $alerta_tipo = "error";
            } else {
                $id_escapado = mysqli_real_escape_string($con, $id_cliente);
                $usuario_escapado = mysqli_real_escape_string($con, $usuario_cred);
                
                $sql_update_cred = "UPDATE cliente_usuario SET usuario = '$usuario_escapado'";
                
                if (!empty($contrasena)) {
                    $contrasena_escapada = mysqli_real_escape_string($con, $contrasena);
                    $sql_update_cred .= ", contrasena = '$contrasena_escapada'";
                }
                
                $sql_update_cred .= " WHERE id_cliente = '$id_escapado'";
                
                if (mysqli_query($con, $sql_update_cred)) {
                    $alerta_mensaje = "✅ Credenciales modificadas exitosamente.";
                    $alerta_tipo = "success";
                    
                    $id_cliente = $nombre_completo = $dni = $direccion = $telefono = $correo = $usuario_cred = $contrasena = $conf_contrasena = '';
                } else {
                    $alerta_mensaje = "❌ ERROR al modificar: " . mysqli_error($con);
                    $alerta_tipo = "error";
                }
            }
        }
        elseif ($accion == 'buscar') {
            
            if (!empty($nombre_completo)) {
                $nombre_escapado = mysqli_real_escape_string($con, $nombre_completo);
                
                $sql_buscar = "SELECT c.*, cu.usuario, cu.contrasena 
                               FROM cliente c 
                               LEFT JOIN cliente_usuario cu ON c.id_cliente = cu.id_cliente
                               WHERE c.nombre_completo LIKE '%$nombre_escapado%' LIMIT 1";
                
                $resultado_busqueda = mysqli_query($con, $sql_buscar);
                
                if ($resultado_busqueda && mysqli_num_rows($resultado_busqueda) > 0) {
                    $cliente_encontrado = mysqli_fetch_assoc($resultado_busqueda);
                    
                    $id_cliente = $cliente_encontrado['id_cliente'];
                    $nombre_completo = $cliente_encontrado['nombre_completo'];
                    $dni = $cliente_encontrado['dni'];
                    $direccion = $cliente_encontrado['direccion'];
                    $telefono = $cliente_encontrado['telefono'];
                    $correo = $cliente_encontrado['correo'];
                    $usuario_cred = $cliente_encontrado['usuario'] ?? '';
                    $contrasena = $cliente_encontrado['contrasena'] ?? '';
                    $conf_contrasena = $contrasena;
                    
                    $alerta_mensaje = "Datos cargados. ID: {$id_cliente}.";
                    $alerta_tipo = "info";
                } else {
                    $alerta_mensaje = "No se encontró cliente con ese nombre.";
                    $alerta_tipo = "warning";
                    $id_cliente = ''; 
                }
            } else {
                $alerta_mensaje = "Ingrese un nombre para buscar.";
                $alerta_tipo = "info";
            }
        }
    }
}

$sql_tabla = "SELECT c.id_cliente, c.nombre_completo, c.dni, c.direccion, c.telefono, c.correo, cu.usuario
              FROM cliente c
              LEFT JOIN cliente_usuario cu ON c.id_cliente = cu.id_cliente
              ORDER BY c.id_cliente ASC";
$datos = mysqli_query($con, $sql_tabla);

$id_cliente_html = $id_cliente;
$apnom_html = $nombre_completo;
$dni_html = $dni;
$direccion_html = $direccion;
$telefono_html = $telefono;
$correo_html = $correo;
$usuario_html = $usuario_cred;
$contrasena_html = $contrasena;
$conf_contrasena_html = $conf_contrasena;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Gestion de Usuarios Clientes</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="imagenes/DC_Logo_Cabecera.png"/>
        <link rel="stylesheet" href="CSS/formClienteUsuario.css"/>
        <style>
            .alerta-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .tabla-cliente tbody tr { cursor: default; }
        </style>
    </head>
    <body>
        <main>
            <h1>CEVICHERIA DON CANGREJO</h1>
            <h2>Gestión de Clientes Usuarios</h2>

            <?php if (!empty($alerta_mensaje)): ?>
                <div class="alerta-<?= $alerta_tipo ?>"><?= $alerta_mensaje ?></div>
            <?php endif; ?>
            
            <form id="formUsuarioCliente" action="formUsuarioCliente.php" method="post">
                <input type="hidden" id="id_cliente" name="id_cliente" value="<?= htmlspecialchars($id_cliente_html) ?>"/>
                
                <div class="form-group">
                    <label for="apnom">Nombre y Apellido</label>
                    <input type="text" id="apnom" name="apnom" value="<?= htmlspecialchars($apnom_html) ?>" required/>
                </div>
                <div class="form-group">
                    <label for="dni">DNI</label>
                    <input type="text" id="dni" name="dni" maxlength="8" value="<?= htmlspecialchars($dni_html) ?>" readonly/>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($direccion_html) ?>" readonly/>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" maxlength="9" value="<?= htmlspecialchars($telefono_html) ?>" readonly/>
                </div>
                <div class="form-group">
                    <label for="correo">Correo</label>
                    <input type="text" id="correo" name="correo" value="<?= htmlspecialchars($correo_html) ?>" readonly/>
                </div>
                
                <hr>
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario_html) ?>"/>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="text" id="contrasena" name="contrasena" value="<?= htmlspecialchars($contrasena_html) ?>"/>
                </div>
                <div class="form-group">
                    <label for="conf_contrasena">Confirmar Contraseña</label>
                    <input type="text" id="conf_contrasena" name="conf_contrasena" value="<?= htmlspecialchars($conf_contrasena_html) ?>"/>
                </div>
                
                <div class="grupo-botones">
                    <button type="submit" name="accion" value="buscar" class="btn btn-buscar">Buscar</button>
                    
                    <button type="submit" name="accion" value="guardar" class="btn btn-guardar" 
                        <?= empty($id_cliente_html) ? 'disabled' : '' ?>>Guardar Nuevo</button>
                        
                    <button type="submit" name="accion" value="modificar" class="btn btn-modificar" 
                        <?= empty($id_cliente_html) ? 'disabled' : '' ?>>Modificar</button>
                        
                    <button type="submit" name="accion" value="eliminar" class="btn btn-eliminar" 
                        <?= empty($id_cliente_html) ? 'disabled' : '' ?>>Eliminar</button>
                    
                    <button type="submit" name="accion" value="limpiar" class="btn btn-limpiar">Limpiar</button>
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
                                <th>Usuario</th>
                                <th>DNI</th>
                                <th>Telefono</th>
                                <th>Correo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($datos && mysqli_num_rows($datos) > 0) {
                                while($row = mysqli_fetch_array($datos, MYSQLI_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id_cliente']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nombre_completo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['usuario'] ?? '') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['dni']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
                                    echo "</tr>";
                                }
                                mysqli_free_result($datos);
                            } else {
                                echo "<tr><td colspan='6' style='text-align: center;'>No se encontraron clientes registrados.</td></tr>";
                            }
                            if (isset($con)) mysqli_close($con);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </body>
</html>