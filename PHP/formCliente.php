<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
    <?php
    include 'conexion.php';
    mysqli_select_db($db, "nombredebasedatos");
    $idcliente=$_POST['id_cliente'];
    $apnom=$_POST['apnom'];
    $dni=$_POST['dni'];
    $direccion=$_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $accion = $_POST['accion'];
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
    mysqli_close($db);
    ?>
    </body>
</html>
