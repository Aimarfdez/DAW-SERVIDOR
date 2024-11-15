<?php
include_once 'app/funciones.php';
ob_start();
$msg = "";

if (!isset($_REQUEST['orden'])) {
    include_once 'app/entrada.php';
} else {
    switch ($_REQUEST['orden']) {
        case "Entrar":
            if (isset($_REQUEST['nombre'], $_REQUEST['contraseña']) &&
                usuarioOK($_REQUEST['nombre'], $_REQUEST['contraseña'])) {
                echo "Bienvenido <b>" . htmlspecialchars($_REQUEST['nombre']) . "</b><br>";
                include_once 'app/comentario.html';
            } else {
                include_once 'app/entrada.php';
                echo "<br><span style='color:red;'>Usuario no válido</span><br>";
            }
            break;
        case "Nueva opinión":
            echo "Nueva opinión<br>";
            include_once 'app/comentario.html';
            break;
        case "Detalles":
            echo "Detalles de su opinión<br>";
            include_once 'app/comentariorelleno.php';
            include_once 'app/detalles.php';
            break;
        case "Terminar":
            include_once 'app/entrada.php';
            break;
    }
}

$contenido_php = ob_get_clean();
include_once "app/plantillas/principal.php";
?>
