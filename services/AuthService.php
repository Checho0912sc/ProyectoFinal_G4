<?php

declare(strict_types=1);

final class AuthService
{
    public function __construct(
        private readonly UsuarioRepository $usuarioRepository
    ) {
    }

    public function autenticar(
        string $correo,
        string $contrasena
    ): array {
        $correo = strtolower(trim($correo));
        $errores = [];

        if ($correo === '') {
            $errores['correo'] = 'El correo es obligatorio.';
        } elseif (
            !filter_var($correo, FILTER_VALIDATE_EMAIL)
        ) {
            $errores['correo'] = 'Ingresa un correo válido.';
        } elseif (strlen($correo) > 190) {
            $errores['correo'] =
                'El correo supera el tamaño permitido.';
        }

        if ($contrasena === '') {
            $errores['contrasena'] =
                'La contraseña es obligatoria.';
        } elseif (strlen($contrasena) > 255) {
            $errores['contrasena'] =
                'La contraseña supera el tamaño permitido.';
        }

        if ($errores !== []) {
            return [
                'exito' => false,
                'errores' => $errores,
            ];
        }

        $usuario = $this->usuarioRepository
            ->buscarPorCorreo($correo);

        if (
            $usuario === null
            || !$usuario->estaActivo()
            || !$usuario->verificarContrasena($contrasena)
        ) {
            return [
                'exito' => false,
                'errores' => [
                    'general' =>
                        'El correo o la contraseña son incorrectos.',
                ],
            ];
        }

        $membresias = $this->usuarioRepository
            ->obtenerMembresiasActivas($usuario->id());

        return [
            'exito' => true,
            'usuario' => $usuario,
            'membresias' => $membresias,
        ];
    }

    public function registrarCuenta(
        array $datos
    ): array {
        $nombre = trim(
            (string) ($datos['nombre'] ?? '')
        );

        $correo = strtolower(
            trim(
                (string) ($datos['correo'] ?? '')
            )
        );

        $telefono = trim(
            (string) ($datos['telefono'] ?? '')
        );

        $contrasena = (string) (
            $datos['contrasena'] ?? ''
        );

        $confirmarContrasena = (string) (
            $datos['confirmar_contrasena'] ?? ''
        );

        $errores = [];

        if (mb_strlen($nombre) < 3) {
            $errores['nombre'] =
                'El nombre debe tener al menos 3 caracteres.';
        }

        if (
            !filter_var(
                $correo,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errores['correo'] =
                'Ingresa un correo electrónico válido.';
        }

        if (strlen($telefono) > 20) {
            $errores['telefono'] =
                'El teléfono es demasiado largo.';
        }

        if (strlen($contrasena) < 6) {
            $errores['contrasena'] =
                'La contraseña debe tener al menos 6 caracteres.';
        }

        if ($contrasena !== $confirmarContrasena) {
            $errores['confirmar_contrasena'] =
                'Las contraseñas no coinciden.';
        }

        if (
            $correo !== ''
            && $this->usuarioRepository
                ->existeCorreo($correo)
        ) {
            $errores['correo'] =
                'Ya existe una cuenta con ese correo.';
        }

        if ($errores !== []) {
            return [
                'exito' => false,
                'errores' => $errores,
            ];
        }

        $idUsuario = $this
            ->usuarioRepository
            ->crearCuenta([
                'nombre' => $nombre,
                'correo' => $correo,
                'telefono' =>
                    $telefono === ''
                        ? null
                        : $telefono,
                'contrasena_hash' =>
                    password_hash(
                        $contrasena,
                        PASSWORD_DEFAULT
                    ),
            ]);

        return [
            'exito' => true,
            'id_usuario' => $idUsuario,
            'errores' => [],
        ];
    }

    public function registrarUltimoAcceso(
        int $idUsuario
    ): void {
        $this->usuarioRepository
            ->actualizarUltimoAcceso($idUsuario);
    }
}