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

        if ($membresias === []) {
            return [
                'exito' => false,
                'errores' => [
                    'general' =>
                        'Tu cuenta no tiene una comunidad activa asignada.',
                ],
            ];
        }

        return [
            'exito' => true,
            'usuario' => $usuario,
            'membresias' => $membresias,
        ];
    }

    public function registrarUltimoAcceso(
        int $idUsuario
    ): void {
        $this->usuarioRepository
            ->actualizarUltimoAcceso($idUsuario);
    }
}