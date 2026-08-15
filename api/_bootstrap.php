<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/helpers.php';

$scriptName = str_replace(
    '\\',
    '/',
    $_SERVER['SCRIPT_NAME'] ?? ''
);

$directorioApi = dirname($scriptName);
$directorioBase = dirname($directorioApi);

define(
    'BASE_URL',
    in_array(
        $directorioBase,
        ['/', '.', '\\'],
        true
    )
        ? ''
        : rtrim($directorioBase, '/')
);

spl_autoload_register(
    function (string $clase): void {
        $directorios = [
            'core',
            'models',
            'repositories',
            'services',
            'controllers',
        ];

        foreach ($directorios as $directorio) {
            $archivo =
                __DIR__
                . '/../'
                . $directorio
                . '/'
                . $clase
                . '.php';

            if (is_file($archivo)) {
                require_once $archivo;
                return;
            }
        }
    }
);

Auth::iniciarSesion();

header(
    'Content-Type: application/json; charset=utf-8'
);

header(
    'X-Content-Type-Options: nosniff'
);

header(
    'Cache-Control: no-store, no-cache, must-revalidate'
);
