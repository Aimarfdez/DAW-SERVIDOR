<?php

require_once 'lib/fpdf.php';

function generarPDF($id) {
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);

    if (!$cli) {
        die("Cliente no encontrado.");
    }

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    $pdf->Cell(40, 10, 'Detalles del Cliente');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40, 10, 'ID: ' . $cli->id);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Nombre: ' . $cli->first_name);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Apellido: ' . $cli->last_name);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Email: ' . $cli->email);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Genero: ' . $cli->gender);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'IP: ' . $cli->ip_address);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Telefono: ' . $cli->telefono);
    $pdf->Ln();
    if ($cli->imagen) {
        $pdf->Image($cli->imagen, 10, $pdf->GetY(), 50);
    }

    $pdf->Output();
}


function crudBorrar ($id){    
    $db = AccesoDatos::getModelo();
    $resu = $db->borrarCliente($id);
    if ( $resu){
         $_SESSION['msg'] = " El usuario ".$id. " ha sido eliminado.";
    } else {
         $_SESSION['msg'] = " Error al eliminar el usuario ".$id.".";
    }

}

function crudTerminar(){
    AccesoDatos::closeModelo();
    session_destroy();
}
 
function crudAlta(){
    $db = AccesoDatos::getModelo();
    $errores = validarDatos($_POST);
    $imagen = null;

    if (empty($errores)) {
        if (!empty($_FILES['imagen']['name'])) {
            $imagen = subirImagen($_FILES['imagen']);
            if (is_string($imagen)) {
                $errores[] = $imagen;
            }
        }
        if (empty($errores)) {
            $_POST['imagen'] = $imagen;
            $db->addCliente($_POST);
            header("Location: index.php");
        } else {
            include_once "app/views/formulario.php";
        }
    } else {
        include_once "app/views/formulario.php";
    }
}

function crudDetalles($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $cliSiguiente = $db->getClienteSiguiente($id);
    $cliAnterior = $db->getClienteAnterior($id);
    include_once "app/views/detalles.php";
}


function crudModificar($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $cliSiguiente = $db->getClienteSiguiente($id);
    $cliAnterior = $db->getClienteAnterior($id);
    $orden = "Modificar";
    $errores = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errores = validarDatos($_POST, $id);
        $imagen = null;

        if (empty($errores)) {
            if (!empty($_FILES['imagen']['name'])) {
                $imagen = subirImagen($_FILES['imagen']);
                if (is_string($imagen)) {
                    $errores[] = $imagen;
                }
            }
            if (empty($errores)) {
                if ($imagen) {
                    $_POST['imagen'] = $imagen;
                } else {
                    $_POST['imagen'] = $cli->imagen;
                }
                $db->modCliente($id, $_POST);
                header("Location: index.php");
            } else {
                include_once "app/views/formulario.php";
            }
        } else {
            include_once "app/views/formulario.php";
        }
    } else {
        include_once "app/views/formulario.php";
    }
}
function crudPostAlta(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    // !!!!!! No se controlan que los datos sean correctos 
    $cli = new Cliente();
    $cli->id            =$_POST['id'];
    $cli->first_name    =$_POST['first_name'];
    $cli->last_name     =$_POST['last_name'];
    $cli->email         =$_POST['email'];	
    $cli->gender        =$_POST['gender'];
    $cli->ip_address    =$_POST['ip_address'];
    $cli->telefono      =$_POST['telefono'];
    $db = AccesoDatos::getModelo();
    if ( $db->addCliente($cli) ) {
           $_SESSION['msg'] = " El usuario ".$cli->first_name." se ha dado de alta ";
        } else {
            $_SESSION['msg'] = " Error al dar de alta al usuario ".$cli->first_name."."; 
        }
}

function crudPostModificar(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    $cli = new Cliente();

    $cli->id            =$_POST['id'];
    $cli->first_name    =$_POST['first_name'];
    $cli->last_name     =$_POST['last_name'];
    $cli->email         =$_POST['email'];	
    $cli->gender        =$_POST['gender'];
    $cli->ip_address    =$_POST['ip_address'];
    $cli->telefono      =$_POST['telefono'];
    $db = AccesoDatos::getModelo();
    if ( $db->modCliente($cli) ){
        $_SESSION['msg'] = " El usuario ha sido modificado";
    } else {
        $_SESSION['msg'] = " Error al modificar el usuario ";
    }
    
}
function validarDatos($datos, $id = null) {
    $errores = [];
    $db = AccesoDatos::getModelo();

    // Validar correo electrónico
    if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo electrónico no válido.";
    } elseif ($db->emailExists($datos['email'], $id)) {
        $errores[] = "El correo electrónico ya está en uso.";
    }

    // Validar IP
    if (!filter_var($datos['ip_address'], FILTER_VALIDATE_IP)) {
        $errores[] = "Dirección IP no válida.";
    }

    // Validar teléfono
    if (!preg_match('/^\d{3}-\d{3}-\d{4}$/', $datos['telefono'])) {
        $errores[] = "El formato del teléfono debe ser 999-999-9999.";
    }

    return $errores;
}

function subirImagen($file) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    $maxSize = 500 * 1024; // 500 KB

    if (!in_array($file['type'], $allowedTypes)) {
        return "El archivo debe ser una imagen JPG o PNG.";
    }

    if ($file['size'] > $maxSize) {
        return "El tamaño del archivo debe ser inferior a 500 KB.";
    }

    $uploadDir = 'uploads/';
    $fileName = uniqid() . '_' . basename($file['name']);
    $uploadFile = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        return $uploadFile;
    } else {
        return "Error al subir la imagen.";
    }
}

function crudLista($orden = 'id'){
    $db = AccesoDatos::getModelo();
    $clientes = $db->getClientesOrdenados($orden);
    include_once "app/views/lista.php";
}