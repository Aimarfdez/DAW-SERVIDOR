<?php
// Mostrar el formulario si la petición es por GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include 'captura.html';
} else {
    // Procesar el formulario si la petición es por POST
    $nombre = htmlspecialchars($_POST['nombre']);
    $alias = htmlspecialchars($_POST['alias']);
    $imagen_subida = false;
    $mensaje_error = "";

    // Validación y control de la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['imagen']['size'] <= 10240 && mime_content_type($_FILES['imagen']['tmp_name']) === 'image/png') {
            $ruta_destino = 'uploads/' . basename($_FILES['imagen']['name']);
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                $imagen_subida = true;
                $imagen = $ruta_destino;
            } else {
                $mensaje_error = "Hubo un error al subir la imagen.";
                $imagen = "calavera.png";
            }
        } else {
            $mensaje_error = "Archivo no permitido o tamaño excedido.";
            $imagen = "calavera.png";
        }
    } else {
        $imagen = "calavera.png";
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Datos del Jugador</title>
    </head>
    <body>
        <h1>Datos del Jugador</h1>
        <p><strong>Nombre:</strong> <?= $nombre; ?></p>
        <p><strong>Alias:</strong> <?= $alias; ?></p>
        <p><strong>Imagen:</strong></p>
        <img src="<?= $imagen; ?>" alt="Imagen del jugador" style="max-width: 200px;">
        <?php if ($mensaje_error) : ?>
            <p style="color: red;"><?= $mensaje_error; ?></p>
        <?php endif; ?>
    </body>
    </html>
    <?php
}
?>
