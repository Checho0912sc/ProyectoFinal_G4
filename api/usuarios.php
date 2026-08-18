<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';

function responderJson(
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

function leerJson(): array
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
            'El contenido enviado no es válido.'
        );
    }

    return $datos;
}

if (!Auth::check()) {
    responderJson(
        [
            'exito' => false,
            'mensaje' =>
                'Debes iniciar sesión.',
        ],
        401
    );
}

if (!Auth::tieneRol('Administrador')) {
    responderJson(
        [
            'exito' => false,
            'mensaje' =>
                'No tienes permisos para administrar usuarios.',
        ],
        403
    );
}

$usuarioSesion = Auth::usuario();

$idComunidad =
    (int) (
        $usuarioSesion['id_comunidad']
        ?? 0
    );

$idUsuarioActual =
    (int) (
        $usuarioSesion['id_usuario']
        ?? 0
    );

if ($idComunidad <= 0) {
    responderJson(
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

$idUsuario = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

$recurso = strtolower(
    trim(
        (string) ($_GET['recurso'] ?? '')
    )
);

$controlador = new UsuarioController();

try {
    if ($metodo === 'GET') {
        if ($recurso === 'roles') {
            responderJson([
                'exito' => true,
                'roles' =>
                    $controlador
                        ->listarRoles(),
            ]);
        }

        if (
            $idUsuario !== false
            && $idUsuario !== null
        ) {
            responderJson([
                'exito' => true,
                'usuario' =>
                    $controlador->obtener(
                        (int) $idUsuario,
                        $idComunidad
                    ),
            ]);
        }

        responderJson([
            'exito' => true,
            'usuarios' =>
                $controlador->listar(
                    $idComunidad
                ),
        ]);
    }

    $csrfToken =
        $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? null;

    if (!Auth::validarCsrf($csrfToken)) {
        responderJson(
            [
                'exito' => false,
                'mensaje' =>
                    'La sesión del formulario venció. Recarga la página.',
            ],
            419
        );
    }

    if ($metodo === 'POST') {
        $datos = leerJson();

        $nuevoId =
            $controlador->crear(
                $datos,
                $idComunidad
            );

        responderJson(
            [
                'exito' => true,
                'mensaje' =>
                    'Usuario registrado correctamente.',
                'id_usuario' => $nuevoId,
            ],
            201
        );
    }

    if ($metodo === 'PUT') {
        if (
            $idUsuario === false
            || $idUsuario === null
        ) {
            throw new InvalidArgumentException(
                'Debes indicar el usuario que deseas actualizar.'
            );
        }

        $datos = leerJson();

        $controlador->actualizar(
            (int) $idUsuario,
            $datos,
            $idComunidad
        );

        responderJson([
            'exito' => true,
            'mensaje' =>
                'Usuario actualizado correctamente.',
        ]);
    }

    if ($metodo === 'DELETE') {
        if (
            $idUsuario === false
            || $idUsuario === null
        ) {
            throw new InvalidArgumentException(
                'Debes indicar el usuario que deseas desactivar.'
            );
        }

        $controlador->eliminar(
            (int) $idUsuario,
            $idComunidad,
            $idUsuarioActual
        );

        responderJson([
            'exito' => true,
            'mensaje' =>
                'Usuario desactivado correctamente.',
        ]);
    }

    responderJson(
        [
            'exito' => false,
            'mensaje' =>
                'Método HTTP no permitido.',
        ],
        405
    );
} catch (InvalidArgumentException $error) {
    responderJson(
        [
            'exito' => false,
            'mensaje' => $error->getMessage(),
        ],
        422
    );
} catch (RuntimeException $error) {
    responderJson(
        [
            'exito' => false,
            'mensaje' => $error->getMessage(),
        ],
        404
    );
} catch (Throwable $error) {
    error_log($error->__toString());

    responderJson(
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