<?php
session_start();
require 'conexion.php'; 

$id_usuario = '';
$apnom = ''; 
$dni = '';
$direccion = '';
$usuario_input = '';
$rol = '';
$telefono = '';
$correo = '';
$estado = '';
$contrasena = '';
$contrasena_db = '';
$contrasena_placeholder = '•••••••••••••'; 

$alerta_mensaje = "";
$alerta_tipo = "info"; 
$accion = $_POST['accion'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_usuario = $_POST['id_usuario'] ?? '';
    $apnom = $_POST['apnom'] ?? ''; 
    $dni = $_POST['dni'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $usuario_input = $_POST['usuario'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if ($accion == 'eliminar') {
        
        if (!empty($id_usuario)) {
            
            $id_escapado = mysqli_real_escape_string($con, $id_usuario);
            
            $sql_delete = "DELETE FROM usuario_interno WHERE id_usuario = '$id_escapado'";
            
            if (mysqli_query($con, $sql_delete)) {
                $alerta_mensaje = "🗑️ Usuario con ID {$id_usuario} eliminado exitosamente.";
                $alerta_tipo = "success";
                
                $id_usuario = $apnom = $dni = $direccion = $usuario_input = $rol = $telefono = $correo = $estado = $contrasena_db = '';
            } else {
                $alerta_mensaje = "❌ ERROR al eliminar el usuario: " . mysqli_error($con);
                $alerta_tipo = "error";
            }
        } else {
            $alerta_mensaje = "ERROR: Debe buscar un usuario primero para poder eliminarlo.";
            $alerta_tipo = "error";
        }
    }
    elseif ($accion == 'guardar') {
        
        if (empty($apnom) || empty($usuario_input) || empty($contrasena) || empty($rol) || empty($estado)) {
            $alerta_mensaje = "ERROR: Los campos Nombres, Usuario, Contraseña, Rol y Estado son obligatorios.";
            $alerta_tipo = "error";
        } else {
            $apnom_escapado = mysqli_real_escape_string($con, $apnom);
            $dni_escapado = mysqli_real_escape_string($con, $dni);
            $direccion_escapada = mysqli_real_escape_string($con, $direccion);
            $usuario_escapado = mysqli_real_escape_string($con, $usuario_input);
            $rol_escapado = mysqli_real_escape_string($con, $rol);
            $telefono_escapado = mysqli_real_escape_string($con, $telefono);
            $correo_escapado = mysqli_real_escape_string($con, $correo);
            $estado_escapado = mysqli_real_escape_string($con, $estado);

            $contrasena_plana = mysqli_real_escape_string($con, $contrasena);
            
            $sql_insert = "INSERT INTO usuario_interno (apnom, dni, direccion, usuario, contrasena, rol, telefono, correo, estado) 
                           VALUES ('$apnom_escapado', '$dni_escapado', '$direccion_escapada', '$usuario_escapado', 
                                   '$contrasena_plana', '$rol_escapado', '$telefono_escapado', '$correo_escapado', '$estado_escapado')";
            
            if (mysqli_query($con, $sql_insert)) {
                $alerta_mensaje = "✅ Usuario '{$usuario_input}' registrado exitosamente.";
                $alerta_tipo = "success";
                
                $id_usuario = $apnom = $dni = $direccion = $usuario_input = $rol = $telefono = $correo = $estado = $contrasena_db = '';
            } else {
                 if (mysqli_errno($con) == 1062) {
                    $alerta_mensaje = "❌ ERROR: El nombre de usuario '{$usuario_input}' ya existe. Elija otro.";
                    $alerta_tipo = "error";
                 } else {
                    $alerta_mensaje = "❌ ERROR al guardar el usuario: " . mysqli_error($con);
                    $alerta_tipo = "error";
                 }
            }
        }
    } 
    elseif ($accion == 'modificar') {
        
        if (empty($id_usuario) || empty($apnom) || empty($usuario_input) || empty($contrasena) || empty($rol) || empty($estado)) {
            $alerta_mensaje = "ERROR: Para modificar, el ID y los campos Nombres, Usuario, Contraseña, Rol y Estado son obligatorios.";
            $alerta_tipo = "error";
        } else {
            $id_escapado = mysqli_real_escape_string($con, $id_usuario);
            $apnom_escapado = mysqli_real_escape_string($con, $apnom);
            $dni_escapado = mysqli_real_escape_string($con, $dni);
            $direccion_escapada = mysqli_real_escape_string($con, $direccion);
            $usuario_escapado = mysqli_real_escape_string($con, $usuario_input);
            $rol_escapado = mysqli_real_escape_string($con, $rol);
            $telefono_escapado = mysqli_real_escape_string($con, $telefono);
            $correo_escapado = mysqli_real_escape_string($con, $correo);
            $estado_escapado = mysqli_real_escape_string($con, $estado);

            $contrasena_plana = mysqli_real_escape_string($con, $contrasena);
            
            $sql_update = "UPDATE usuario_interno SET 
                           apnom = '$apnom_escapado', 
                           dni = '$dni_escapado', 
                           direccion = '$direccion_escapada', 
                           usuario = '$usuario_escapado', 
                           contrasena = '$contrasena_plana', 
                           rol = '$rol_escapado', 
                           telefono = '$telefono_escapado', 
                           correo = '$correo_escapado', 
                           estado = '$estado_escapado' 
                           WHERE id_usuario = '$id_escapado'";
            
            if (mysqli_query($con, $sql_update)) {
                $alerta_mensaje = "✅ Usuario con ID {$id_escapado} modificado exitosamente.";
                $alerta_tipo = "success";
                
                $id_usuario = $apnom = $dni = $direccion = $usuario_input = $rol = $telefono = $correo = $estado = $contrasena_db = '';
                
            } else {
                 if (mysqli_errno($con) == 1062) {
                    $alerta_mensaje = "❌ ERROR: El nombre de usuario '{$usuario_input}' ya existe o lo está usando otro usuario. No se pudo modificar.";
                    $alerta_tipo = "error";
                 } else {
                    $alerta_mensaje = "❌ ERROR al modificar el usuario: " . mysqli_error($con);
                    $alerta_tipo = "error";
                 }
            }
        }
    }
    elseif ($accion == 'buscar') {
        
        if (!empty($apnom)) {
            $apnom_escapado = mysqli_real_escape_string($con, $apnom);
            
            $sql_buscar = "SELECT id_usuario, apnom, dni, direccion, usuario, contrasena, rol, telefono, correo, estado 
                           FROM usuario_interno 
                           WHERE apnom LIKE '%$apnom_escapado%'";
            
            $resultado_busqueda = mysqli_query($con, $sql_buscar);
            
            if ($resultado_busqueda) {
                if (mysqli_num_rows($resultado_busqueda) == 1) {
                    $usuario_encontrado = mysqli_fetch_assoc($resultado_busqueda);
                    
                    $id_usuario = $usuario_encontrado['id_usuario'];
                    $apnom = $usuario_encontrado['apnom'];
                    $dni = $usuario_encontrado['dni'];
                    $direccion = $usuario_encontrado['direccion'];
                    $usuario_input = $usuario_encontrado['usuario'];
                    $rol = $usuario_encontrado['rol'];
                    $telefono = $usuario_encontrado['telefono'];
                    $correo = $usuario_encontrado['correo'];
                    $estado = $usuario_encontrado['estado'];
                    $contrasena_db = $usuario_encontrado['contrasena']; 
                    $contrasena_placeholder = 'Contraseña cargada';
                    
                    $alerta_mensaje = "Usuario cargado. ID: {$id_usuario}. Contraseña visible.";
                    $alerta_tipo = "info";
                } else if (mysqli_num_rows($resultado_busqueda) > 1) {
                    $alerta_mensaje = "Múltiples usuarios encontrados con ese nombre. Sea más específico en la búsqueda.";
                    $alerta_tipo = "info";
                } else {
                    $alerta_mensaje = "No se encontró ningún usuario con ese nombre.";
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


$sql_tabla = "SELECT id_usuario, apnom, dni, direccion, usuario, contrasena, rol, telefono, correo, estado 
              FROM usuario_interno 
              ORDER BY id_usuario ASC";

$datos = mysqli_query($con, $sql_tabla);

if (!$datos) {
    $alerta_mensaje = "Error al cargar el listado de usuarios: " . mysqli_error($con);
    $alerta_tipo = "error";
}

$id_usuario_html = $id_usuario;
$apnom_html = $apnom;
$dni_html = $dni;
$direccion_html = $direccion;
$usuario_input_html = $usuario_input;
$rol_html = $rol;
$telefono_html = $telefono;
$correo_html = $correo;
$estado_html = $estado;

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Gestion Usuarios Internos</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="imagenes/DC_Logo_Cabecera.png"/>
        <link rel="stylesheet" href="CSS/formUsuarioInterno.css"/> 
        <style>
            .alerta-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .alerta-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
            .tabla-usuarios tbody tr {
                cursor: default;
            }
            .tabla-usuarios tbody tr:hover {
                background-color: #f5f5f5;
            }
        </style>
    </head>
    <body>
        
        <main>
            <h1>CEVICHERIA DON CANGREJO</h1>
            <h2>Gestión de Usuarios Internos</h2>
            
            <?php if (!empty($alerta_mensaje)): ?>
                <div class="alerta-<?= $alerta_tipo ?>"><?= $alerta_mensaje ?></div>
            <?php endif; ?>

            <form id="formUsuarioInterno" action="formUsuarioInterno.php" method="POST">
                
                <input type="hidden" id="id_usuario" name="id_usuario" value="<?= htmlspecialchars($id_usuario_html) ?>">
                
                <div class="form-group">
                    <label for="apnom">Nombres y Apellidos</label>
                    <input type="text" id="apnom" name="apnom" value="<?= htmlspecialchars($apnom_html) ?>" placeholder="Ingrese apellidos o nombres para buscar" required />
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
                    <label for="usuario_input">Usuario</label>
                    <input type="text" id="usuario_input" name="usuario" value="<?= htmlspecialchars($usuario_input_html) ?>" />
                </div>
                
                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="text" id="contrasena" name="contrasena" value="<?= htmlspecialchars($contrasena_db) ?>" placeholder="<?= $contrasena_placeholder ?>" >
                </div>
                
                <div class="form-group">
                    <label for="rol">Rol</label>
                    <select id="rol" name="rol">
                        <option value="" disabled <?= empty($rol_html) ? 'selected' : '' ?>>Seleccione el rol</option>
                        <option value="Administrador" <?= $rol_html == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                        <option value="Mesero" <?= $rol_html == 'Mesero' ? 'selected' : '' ?>>Mesero</option>
                        <option value="Cocinero" <?= $rol_html == 'Cocinero' ? 'selected' : '' ?>>Cocinero</option>
                        <option value="Cajero" <?= $rol_html == 'Cajero' ? 'selected' : '' ?>>Cajero</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($telefono_html) ?>"/>
                </div>
                
                <div class="form-group">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($correo_html) ?>"/>
                </div>
                
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="" disabled <?= empty($estado_html) ? 'selected' : '' ?>>Seleccione el estado</option>
                        <option value="Activo" <?= $estado_html == 'Activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="Inactivo" <?= $estado_html == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>

                <div class="grupo-botones">
                    <button type="submit" name="accion" value="buscar" class="btn btn-buscar">Buscar</button>
                    
                    <button type="submit" name="accion" value="guardar" class="btn btn-guardar" <?= !empty($id_usuario_html) ? 'disabled' : '' ?>>Guardar Nuevo</button>
                    <button type="submit" name="accion" value="modificar" class="btn btn-modificar" <?= empty($id_usuario_html) ? 'disabled' : '' ?>>Modificar</button>
                    <button type="submit" name="accion" value="eliminar" class="btn btn-eliminar" <?= empty($id_usuario_html) ? 'disabled' : '' ?>>Eliminar</button>
                    
                    <button type="button" onclick="window.location.href='formUsuarioInterno.php'" class="btn btn-limpiar">Limpiar Pantalla</button>
                </div>
            </form>
            
            <div class="tabla-usuarios-container">
                <h2>Listado de Usuarios Internos</h2>
                
                <div class="table-responsive">
                    <table class="tabla-usuarios">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Apellidos y Nombres</th>
                                <th>DNI</th>
                                <th>Dirección</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($datos && mysqli_num_rows($datos) > 0) {
                                while($row = mysqli_fetch_array($datos, MYSQLI_ASSOC)) {
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['id_usuario']) ?></td>
                                        <td><?= htmlspecialchars($row['apnom']) ?></td>
                                        <td><?= htmlspecialchars($row['dni']) ?></td>
                                        <td><?= htmlspecialchars($row['direccion']) ?></td>
                                        <td><?= htmlspecialchars($row['usuario']) ?></td>
                                        <td><?= htmlspecialchars($row['rol']) ?></td>
                                        <td><?= htmlspecialchars($row['telefono']) ?></td>
                                        <td><?= htmlspecialchars($row['correo']) ?></td>
                                        <td><?= htmlspecialchars($row['estado']) ?></td>
                                    </tr>
                                    <?php
                                }
                                mysqli_free_result($datos);
                            } else {
                                ?>
                                <tr>
                                    <td colspan="9" style="text-align: center;">No se encontraron usuarios registrados.</td>
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