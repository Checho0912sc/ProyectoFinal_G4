<?php

declare(strict_types=1);

abstract class Controller
{
    protected function render( //carga de header y footer
        string $vista,
        array $datos = [],
        int $estadoHttp = 200
    ): void {
        $archivoVista = __DIR__ . '/../views/' . $vista . '.php';

        if (!is_file($archivoVista)) {
            throw new RuntimeException(
                'No se encontró la vista solicitada.'
            );
        }

        http_response_code($estadoHttp);

        extract($datos, EXTR_SKIP);

        require __DIR__ . '/../views/layout/header.php';
        require $archivoVista;
        require __DIR__ . '/../views/layout/footer.php';
    }

    protected function redirect(string $ruta): never //redireccionamiento del usuario a la ruta solicitada
    {
        header('Location: ' . url($ruta));
        exit;
    }

    protected function exigirMetodo(string $metodo): void //validacion de metodos
    {
        $metodoRecibido = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (strtoupper($metodoRecibido) !== strtoupper($metodo)) {
            http_response_code(405);
            header('Allow: ' . strtoupper($metodo));
            exit('Método no permitido.');
        }
    }
}