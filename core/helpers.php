<?php

declare(strict_types=1);

/* Construye una URL tomando en cuenta la carpeta del proyecto.*/
function url(string $ruta = ''): string
{
    $base = defined('BASE_URL') ? BASE_URL : '';

    if ($ruta === '') {
        return $base . '/';
    }

    return $base . '/' . ltrim($ruta, '/');
}

/* Convierte caracteres especiales para mostrarlos de forma segura.*/
function e(mixed $valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

function colones(
    float|int|string $monto
): string {
    return '₡' . number_format(
        (float) $monto,
        2,
        ',',
        '.'
    );
}

function fechaCorta(string $fecha): string
{
    $valor = DateTimeImmutable::createFromFormat(
        'Y-m-d',
        $fecha
    );

    if ($valor === false) {
        return $fecha;
    }

    return $valor->format('d/m/Y');
}

function horaCorta(string $hora): string
{
    return substr($hora, 0, 5);
}