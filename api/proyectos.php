<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';

function responderProyectoJson(
    array $datos,
    int $codigo = 200
): never {
    http_response_code($codigo);

    echo json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
    );

    exit;
}

function leerProyectoJson(): array
{
    $contenido =
        file_get_contents('php://input');

    if (
        $contenido === false
        || trim($contenido) === ''
    ) {
        return [];
    }

    $datos = json_decode(
        $contenido,
        true
    );

    if (!is_array($datos)) {
        throw new InvalidArgumentException(
            'Los datos enviados no son válidos.'
        );
    }

    return $datos;
}

if (!Auth::check()) {
    responderProyectoJson(
        [
            'exito' => false,
            'mensaje' =>
                'Debes iniciar sesión.',
        ],
        401
    );
}

$usuarioSesion = Auth::usuario();

$idComunidad =
    (int) (
        $usuarioSesion['id_comunidad']
        ?? 0
    );

if ($idComunidad <= 0) {
    responderProyectoJson(
        [
            'exito' => false,
            'mensaje' =>
                'No existe una comunidad activa en la sesión.',
        ],
        400
    );
}

$metodo = strtoupper(
    $_SERVER['REQUEST_METHOD'] ?? 'GET'
);

$metodosEscritura = [
    'POST',
    'PUT',
    'DELETE',
];

if (
    in_array(
        $metodo,
        $metodosEscritura,
        true
    )
    && !Auth::tieneRol(
        'Administrador',
        'Coordinador'
    )
) {
    responderProyectoJson(
        [
            'exito' => false,
            'mensaje' =>
                'No tienes permisos para gestionar proyectos.',
        ],
        403
    );
}

$idProyecto = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

$recurso = strtolower(
    trim(
        (string) ($_GET['recurso'] ?? '')
    )
);

$controlador =
    new ProyectoController();

try {
    if ($metodo === 'GET') {
        if ($recurso === 'grupos') {
            responderProyectoJson([
                'exito' => true,
                'grupos' =>
                    $controlador
                        ->listarGrupos(
                            $idComunidad
                        ),
            ]);
        }

        if (
            $recurso
            === 'responsables'
        ) {
            responderProyectoJson([
                'exito' => true,
                'responsables' =>
                    $controlador
                        ->listarResponsables(
                            $idComunidad
                        ),
            ]);
        }

        if (
            $idProyecto !== false
            && $idProyecto !== null
        ) {
            responderProyectoJson([
                'exito' => true,
                'proyecto' =>
                    $controlador->obtener(
                        (int) $idProyecto,
                        $idComunidad
                    ),
            ]);
        }

        responderProyectoJson([
            'exito' => true,
            'proyectos' =>
                $controlador->listar(
                    $idComunidad
                ),
        ]);
    }

    $csrfToken =
        $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? null;

    if (!Auth::validarCsrf($csrfToken)) {
        responderProyectoJson(
            [
                'exito' => false,
                'mensaje' =>
                    'La sesión del formulario venció. Recarga la página.',
            ],
            419
        );
    }

    if ($metodo === 'POST') {
        $datos = leerProyectoJson();

        $nuevoId =
            $controlador->crear(
                $datos,
                $idComunidad
            );

        responderProyectoJson(
            [
                'exito' => true,
                'mensaje' =>
                    'Proyecto registrado correctamente.',
                'id_proyecto' => $nuevoId,
            ],
            201
        );
    }

    if ($metodo === 'PUT') {
        if (
            $idProyecto === false
            || $idProyecto === null
        ) {
            throw new InvalidArgumentException(
                'Debes indicar el proyecto que deseas actualizar.'
            );
        }

        $datos = leerProyectoJson();

        $controlador->actualizar(
            (int) $idProyecto,
            $datos,
            $idComunidad
        );

        responderProyectoJson([
            'exito' => true,
            'mensaje' =>
                'Proyecto actualizado correctamente.',
        ]);
    }

    if ($metodo === 'DELETE') {
        if (
            $idProyecto === false
            || $idProyecto === null
        ) {
            throw new InvalidArgumentException(
                'Debes indicar el proyecto que deseas cancelar.'
            );
        }

        $controlador->eliminar(
            (int) $idProyecto,
            $idComunidad
        );

        responderProyectoJson([
            'exito' => true,
            'mensaje' =>
                'Proyecto cancelado correctamente.',
        ]);
    }

    responderProyectoJson(
        [
            'exito' => false,
            'mensaje' =>
                'Método HTTP no permitido.',
        ],
        405
    );
} catch (InvalidArgumentException $error) {
    responderProyectoJson(
        [
            'exito' => false,
            'mensaje' =>
                $error->getMessage(),
        ],
        422
    );
} catch (RuntimeException $error) {
    responderProyectoJson(
        [
            'exito' => false,
            'mensaje' =>
                $error->getMessage(),
        ],
        404
    );
} catch (Throwable $error) {
    error_log(
        $error->__toString()
    );

    responderProyectoJson(
        [
            'exito' => false,
            'mensaje' =>
                APP_DEBUG
                    ? $error->getMessage()
                    : 'Ocurrió un error interno.',
        ],
        500
    );
}