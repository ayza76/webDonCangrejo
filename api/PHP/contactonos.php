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
        
        $nombre=$_POST['nombre'];  
        $correo=$_POST['correo'];
        $telefono=$_POST['telefono'];
        $mensaje=$_POST['mensaje'];
        
        ?>
    </body>
</html>
