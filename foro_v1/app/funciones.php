<?php
function usuarioOK($nombre, $contraseña) {
    return strlen($nombre) >= 8 && $contraseña === strrev($nombre);
}

function analizarTexto($texto) {
    $numCaracteres = strlen($texto);
    $palabras = str_word_count($texto, 1);
    $numPalabras = count($palabras);
    $caracteresFrecuencia = count_chars($texto, 1);
    $caracterMasRepetido = chr(array_search(max($caracteresFrecuencia), $caracteresFrecuencia));
    $palabrasFrecuencia = array_count_values($palabras);
    arsort($palabrasFrecuencia);
    $palabraMasRepetida = array_key_first($palabrasFrecuencia);

    return [
        'numCaracteres' => $numCaracteres,
        'numPalabras' => $numPalabras,
        'caracterMasRepetido' => $caracterMasRepetido,
        'palabraMasRepetida' => $palabraMasRepetida,
    ];
}
?>
