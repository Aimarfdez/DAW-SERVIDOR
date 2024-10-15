<?php

define('DADO_1', '&#9856;');
define('DADO_2', '&#9857;');
define('DADO_3', '&#9858;');
define('DADO_4', '&#9859;');
define('DADO_5', '&#9860;');
define('DADO_6', '&#9861;');

function lanzarDados($numDados = 6)
{
    $dados = [];
    for ($i = 0; $i < $numDados; $i++) {
        $dados[] = rand(1, 6);
    }
    return $dados;
}

function calcularPuntos($dados)
{
    sort($dados);
    array_shift($dados);
    array_pop($dados);
    return array_sum($dados);
}

$dadosJugador1 = lanzarDados();
$dadosJugador2 = lanzarDados();

$puntuacionJugador1 = calcularPuntos($dadosJugador1);
$puntuacionJugador2 = calcularPuntos($dadosJugador2);

$ganador = '';
if ($puntosJugador1 > $puntosJugador2) {
    $ganador = 'Jugador 1';
} elseif ($puntosJugador2 > $puntosJugador1) {
    $ganador = 'Jugador 2';
} else {
    $ganador = 'Empate';
}

function convertirEnDados($dados)
{
    $simbolos = '';
    foreach ($dados as $dado) {
        switch ($dado) {
            case 1:
                $simbolos .= DADO_1;
                break;
            case 2:
                $simbolos .= DADO_2;
                break;
            case 3:
                $simbolos .= DADO_3;
                break;
            case 4:
                $simbolos .= DADO_4;
                break;
            case 5:
                $simbolos .= DADO_5;
                break;
            case 6:
                $simbolos .= DADO_6;
                break;
        }
    }
    return $simbolos;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Dados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }

        .jugador {
            padding: 20px;
            margin: 20px;
            border: 2px solid black;
            display: inline-block;
            background-color: #fff;
        }

        .rojo {
            background-color: #ffcccc;
        }

        .azul {
            background-color: #ccccff;
        }

        .dados {
            font-size: 60px;
        }
    </style>
</head>

<body>

    <h1>Juego de Dados</h1>

    <div class="jugador rojo">
        <h2>Jugador 1</h2>
        <p class="dados"><?php echo convertirEnDados($dadosJugador1); ?></p>
        <p><?php echo $puntosJugador1; ?> puntos</p>
    </div>

    <div class="jugador azul">
        <h2>Jugador 2</h2>
        <p class="dados"><?php echo convertirEnDados($dadosJugador2); ?></p>
        <p><?php echo $puntosJugador2; ?> puntos</p>
    </div>

    <h2>Resultado: Ha Ganado el <?php echo $ganador; ?></h2>

    <p><button onclick="location.reload();">Jugar de nuevo</button></p>

</body>

</html>
