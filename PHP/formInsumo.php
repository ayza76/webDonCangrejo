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
        $stockactual=$_POST['stock_actual'];
        $stockmin=$_POST['stock_minimo'];
        $stockmedir=$_POST['stock_medida'];
        
        ?>
    </body>
</html>
