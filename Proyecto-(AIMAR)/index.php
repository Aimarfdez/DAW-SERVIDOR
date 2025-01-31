<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
define('FPAG', 10); // Número de filas por página

require_once 'app/helpers/util.php';
require_once 'app/config/configDB.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/AccesoDatos.php';
require_once 'app/controllers/crudclientes.php';

//---- PAGINACIÓN ----
$midb = AccesoDatos::getModelo();
$totalfilas = $midb->numClientes();
if ($totalfilas % FPAG == 0) {
    $posfin = $totalfilas - FPAG;
} else {
    $posfin = $totalfilas - $totalfilas % FPAG;
}

if (!isset($_SESSION['posini'])) {
    $_SESSION['posini'] = 0;
}
$posAux = $_SESSION['posini'];
//------------

// Borro cualquier mensaje
$_SESSION['msg'] = " ";

ob_start(); // La salida se guarda en el buffer
if ($_SERVER['REQUEST_METHOD'] == "GET") {

    // Proceso las ordenes de navegación
    if (isset($_GET['nav'])) {
        switch ($_GET['nav']) {
            case "Primero":
                $posAux = 0;
                break;
            case "Siguiente":
                $posAux += FPAG;
                if ($posAux > $posfin) $posAux = $posfin;
                break;
            case "Anterior":
                $posAux -= FPAG;
                if ($posAux < 0) $posAux = 0;
                break;
            case "Ultimo":
                $posAux = $posfin;
                break;
        }
        $_SESSION['posini'] = $posAux;
        header('Location: index.php');
        exit();
    }

    // Proceso las ordenes de CRUD
    if (isset($_GET['orden'])) {
        switch ($_GET['orden']) {
            case "Nuevo":
                crudAlta();
                break;
            case "Borrar":
                $id = $_GET['id'];
                crudBorrar($id);
                break;
            case "Modificar":
                $id = $_GET['id'];
                crudModificar($id);
                break;
            case "Detalles":
                $id = $_GET['id'];
                crudDetalles($id);
                break;
            case "Lista":
                $sort = $_GET['sort'] ?? 'id';
                crudLista($sort);
                break;
            case "GenerarPDF":
                $id = $_GET['id'];
                generarPDF($id);
                break;
        }
        exit();
    }
}

// Muestro la página principal
$posini = $_SESSION['posini'];
$tvalores = $midb->getClientes($posini, FPAG);
include_once "app/views/list.php";

$_SESSION['msg'] = ob_get_contents();
ob_end_clean();
?>