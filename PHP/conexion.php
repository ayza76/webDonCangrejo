<?php
    $servidor="localhost";
    $usuario="host";
    $contrasena="";
    $db= mysqli_connect($servidor,$usuario,$contrasena);
    if (!$db)
    {
        die ("La conexion fallo".mysqli_error());
    }
?>