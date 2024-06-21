<?php

function calcular_ev($nombre, $base, $ev, $nivel)
{
    if ($nombre == "hp") {
        $valor = ((((2 * (int) $base) + ((int) $ev / 4)) * $nivel) / 100) + (int) $nivel + 10;
    } else {
        $valor = (((((2 * (int) $base) + ((int) $ev / 4)) * $nivel) / 100) + 5);
    }
    return $valor;
}

function calcular_dano($ataque, $defensa, $nivel, $debilidad, $poder, $stab)
{

    $valor = ((((((2 * $nivel) / 5) + 2) * $poder * ($ataque / $defensa)) / 50) + 2) * $debilidad * $stab;

    return $valor;
}