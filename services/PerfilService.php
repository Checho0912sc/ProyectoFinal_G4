<?php

declare(strict_types=1);

final class PerfilService
{
    public function __construct(
        private readonly UsuarioRepository $usuarioRepository
    ) {
    }


    // ------------------ OBTENER MI PERFIL (Busca los datos del usuario conectado) ------------------

    public function obtener(
        int $idUsuario
    ): array {
        if ($idUsuario <= 0) {
            throw new InvalidArgumentException(
                'El usuario no es válido.'
            );
        }

        $perfil = $this
            ->usuarioRepository
            ->buscarPerfilPorId($idUsuario);

        if ($perfil === null) {
            throw new RuntimeException(
                'No fue posible encontrar el perfil.'
            );
        }

        return $perfil;
    }


    // ------------------ ACTUALIZAR MI PERFIL (Guarda los datos personales y la contraseña si fue escrita) ------------------

    public function actualizar(
        int $idUsuario,
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

        if (
            mb_strlen($nombre) < 3
            || mb_strlen($nombre) > 120
        ) {
            throw new InvalidArgumentException(
                'El nombre debe tener entre 3 y 120 caracteres.'
            );
        }

        if (
            !filter_var(
                $correo,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new InvalidArgumentException(
                'Debes escribir un correo válido.'
            );
        }

        if (mb_strlen($correo) > 191) {
            throw new InvalidArgumentException(
                'El correo es demasiado largo.'
            );
        }

        if (mb_strlen($telefono) > 20) {
            throw new InvalidArgumentException(
                'El teléfono es demasiado largo.'
            );
        }

        if (
            $this
                ->usuarioRepository
                ->existeCorreo(
                    $correo,
                    $idUsuario
                )
        ) {
            throw new InvalidArgumentException(
                'Ese correo ya pertenece a otra cuenta.'
            );
        }

        $cambiarContrasena =
            $contrasena !== ''
            || $confirmarContrasena !== '';

        if ($cambiarContrasena) {
            if (strlen($contrasena) < 6) {
                throw new InvalidArgumentException(
                    'La nueva contraseña debe tener al menos 6 caracteres.'
                );
            }

            if (
                $contrasena
                !== $confirmarContrasena
            ) {
                throw new InvalidArgumentException(
                    'Las contraseñas no coinciden.'
                );
            }
        }

        $this
            ->usuarioRepository
            ->actualizarPerfil(
                $idUsuario,
                [
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'telefono' =>
                        $telefono === ''
                            ? null
                            : $telefono,
                ]
            );

        if ($cambiarContrasena) {
            $contrasenaHash = password_hash(
                $contrasena,
                PASSWORD_DEFAULT
            );

            $this
                ->usuarioRepository
                ->actualizarContrasena(
                    $idUsuario,
                    $contrasenaHash
                );
        }

        return $this->obtener($idUsuario);
    }
}