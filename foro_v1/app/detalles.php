<?php
if (!empty($_REQUEST['opinion'])) {
    $analisis = analizarTexto($_REQUEST['opinion']);
    echo "<p>Número de caracteres: {$analisis['numCaracteres']}</p>";
    echo "<p>Número de palabras: {$analisis['numPalabras']}</p>";
    echo "<p>Carácter más repetido: {$analisis['caracterMasRepetido']}</p>";
    echo "<p>Palabra más repetida: {$analisis['palabraMasRepetida']}</p>";
} else {
    echo "<p>No hay opinión para analizar.</p>";
}
?>
