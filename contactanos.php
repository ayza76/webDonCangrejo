<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/html.html to edit this template
-->
<html lang="es-PE">
    <head>
        <title>Contacto</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="imagenes/DC_Logo_Cabecera.png">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="CSS/contactenos.css">
    </head>
    <body>
        <header class="contacto-header">
            <a href="index.php" class="back-link">&larr; Página Principal</a>
            <h1 class="titulo-principal">¡Contáctanos!</h1>
            <p class="subtitulo">Estamos listos para atender tus pedidos y consultas.</p>
        </header>

        <main class="contacto-main">
            <div class="contacto-grid">

                <section class="card-form">
                    <h2 class="form-title">Envíanos un Mensaje</h2>
                    <form action="PHP/contactonos.php" method="POST">

                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingresar nombre completo" required>

                        <label for="correo">Correo:</label>
                        <input type="email" id="correo" name="correo" placeholder="Ingresar correo electrónico" required>

                        <label for="telefono">Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" placeholder="Ingresar número telefónico">

                        <label for="mensaje">Mensaje o Consulta:</label>
                        <textarea id="mensaje" name="mensaje" rows="5" placeholder="¿Tienes alguna pregunta o quieres hacer una reserva?"></textarea>

                        <button type="submit" class="btn-enviar">Enviar Mensaje</button>
                    </form>
                </section>

                <section class="card-info">
                    <h2 class="info-title">Datos del Establecimiento</h2>

                    <div class="info-item">
                        <p class="subtitulo">📍 Direcciones:</p>
                        <p>Av. La Marina 123, San Miguel</p>
                        <p>Av. Riva Agüero 456, San Miguel</p>
                    </div>

                    <div class="info-item">
                        <p class="subtitulo">📧 Correo Electrónico:</p>
                        <p>contacto@doncangrejo.com</p>
                    </div>

                    <div class="info-item">
                        <p class="subtitulo">📞 Teléfono:</p>
                        <p>+51 993 509 804</p>
                        <p></p>
                    </div>

                    <div class="info-item redes">
                        <p class="subtitulo">Síguenos:</p>
                        <a href="https://www.facebook.com/profile.php?id=61566410875519" target="_blank">
                            <img src="imagenes/facebook.png" alt="Icono de Facebook" class="icono-redes"/>
                        </a>
                    </div>
                </section>
            </div>

            <section class="mapa-ubicacion">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15607.72750346395!2d-77.08502127538965!3d-12.078494793282246!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105c96ccb6d8595%3A0x63351d5c2d6f6e72!2sSan%20Miguel%2C%20Lima!5e0!3m2!1ses-419!2spe!4v1628100000000!5m2!1ses-419!2spe" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </section>
        </main>
    </body>
</html>