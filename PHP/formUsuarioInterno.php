<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        include 'conexion.php';
        $apenomb=$_POST['apnom'];
        $dni=$_POST['dni'];
        $direccion=$_POST['direccion'];
        $usuario=$_POST['usuario'];
        $contrasena=$_POST['contrasena'];
        $rol=$_POST['rol'];
        $telefono=$_POST['telefono'];
        $correo=$_POST['correo'];
        $estado=$_POST['estado'];
        switch ($accion)
        {   
        case 'guardar':
            if (mysqli_query($db,"basedatos"))
            {
                echo "Cliente guardado exitosamente";
            }
            else
            {
                echo "Error al guardar cliente".mysqli_error($db);
            }
            break;
        case 'modificar':
            if (mysqli_query($db,"basedatos"))
            {
                echo "Cliente modificado exitosamente";
            }
            else
            {
                echo "Error al modificar el cliente".mysqli_error($db);
            }
            break;
        case 'eliminar':
            if (mysqli_query($db,"basedatos"))
            {
                echo "Cliente eliminado exitosamente";
            }
            else
            {
                echo "Error al eliminar cliente".mysqli_error($db);
            }
            break;
        default:
            echo "Accion no valida";
        }
        ?>
    </body>
</html>
