<?php

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/helpers.php';

$directorioBase = str_replace(
    '\\',
    '/',
    dirname(
        $_SERVER['SCRIPT_NAME'] ?? '/'
    )
);

define(
    'BASE_URL',
    in_array(
        $directorioBase,
        ['/', '.'],
        true
    )
        ? ''
        : rtrim(
            $directorioBase,
            '/'
        )
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

        foreach (
            $directorios as $directorio
        ) {

            $archivo =
                __DIR__
                . '/'
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
    'X-Content-Type-Options: nosniff'
);

header(
    'X-Frame-Options: SAMEORIGIN'
);

header(
    'Referrer-Policy: '
    . 'strict-origin-when-cross-origin'
);

header(
    'Cache-Control: '
    . 'no-store, no-cache, must-revalidate, max-age=0'
);

header(
    'Pragma: no-cache'
);

$controladorSolicitado =
    strtolower(
        (string) (
            $_GET['controller']
            ?? 'inicio'
        )
    );

$accionSolicitada =
    strtolower(
        (string) (
            $_GET['action']
            ?? 'index'
        )
    );

$rutas = [

    'inicio' => [

        'index' => [
            InicioController::class,
            'index',
        ],

    ],

    'auth' => [
        'registro' => [
        AuthController::class,
        'registro',
        ],

        'registrar' => [
            AuthController::class,
            'registrar',
        ],
            
        'login' => [
            AuthController::class,
            'login',
        ],

        'autenticar' => [
            AuthController::class,
            'autenticar',
        ],

        'seleccionarcomunidad' => [
            AuthController::class,
            'seleccionarComunidad',
        ],

        'crearcomunidad' => [
            AuthController::class,
            'crearComunidad',
        ],

        'guardarcomunidad' => [
            AuthController::class,
            'guardarComunidad',
        ],

        'confirmarcomunidad' => [
            AuthController::class,
            'confirmarComunidad',
        ],

        'unirsecomunidad' => [
        AuthController::class,
        'unirseComunidad',
        ],

        'logout' => [
            AuthController::class,
            'logout',
        ],
    ],

    'comunidad' => [
        'salir' => [
            ComunidadController::class,
            'salir',
        ],

    ],

    
    'dashboard' => [

        'index' => [
            DashboardController::class,
            'index',
        ],

    ],

    'usuario' => [

        'index' => [
            UsuarioController::class,
            'index',
        ],

    ],

    'perfil' => [

        'index' => [
            PerfilController::class,
            'index',
        ],

        'guardar' => [
            PerfilController::class,
            'guardar',
        ],

    ],

    'proyecto' => [

        'index' => [
            ProyectoController::class,
            'index',
        ],

    ],

    'actividades' => [

        'index' => [
            ActividadController::class,
            'index',
        ],

        'guardar' => [
            ActividadController::class,
            'guardar',
        ],

    ],

    'grupos' => [

        'index' => [
            GrupoController::class,
            'index',
        ],

        'guardar' => [
            GrupoController::class,
            'guardar',
        ],

        'asociar' => [
            GrupoController::class,
            'asociar',
        ],

    ],

];

if (
    !isset(
        $rutas[
            $controladorSolicitado
        ][
            $accionSolicitada
        ]
    )
) {

    http_response_code(404);

    exit(
        'La página solicitada no existe.'
    );

}

[
    $claseControlador,
    $metodo,
] = $rutas[
    $controladorSolicitado
][
    $accionSolicitada
];

try {

    $controlador =
        new $claseControlador();

    $controlador->$metodo();

} catch (Throwable $error) {

    error_log(
        $error->__toString()
    );

    http_response_code(500);

    if (APP_DEBUG) {

        exit(
            'Error interno: '
            . e(
                $error->getMessage()
            )
        );

    }

    exit(
        'Ocurrió un error interno. '
        . 'Inténtalo nuevamente.'
    );

}