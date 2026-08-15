<?php

declare(strict_types=1);

final class UsuarioService
{
    public function __construct(
        private readonly UsuarioRepository $usuarioRepository
    ) {
    }

    public function listar(
        int $idComunidad
    ): array {
        return $this
            ->usuarioRepository
            ->listarPorComunidad($idComunidad);
    }

    public function obtener(
        int $idUsuario,
        int $idComunidad
    ): array {
        if ($idUsuario <= 0) {
            throw new InvalidArgumentException(
                'El usuario solicitado no es válido.'
            );
        }

        $usuario = $this
            ->usuarioRepository
            ->buscarPorIdYComunidad(
                $idUsuario,
                $idComunidad
            );

        if ($usuario === null) {
            throw new RuntimeException(
                'El usuario no existe en esta comunidad.'
            );
        }

        return $usuario;
    }

    public function listarRoles(): array
    {
        return $this
            ->usuarioRepository
            ->listarRolesActivos();
    }

    public function crear(
        array $datos,
        int $idComunidad
    ): int {
        $datosLimpios =
            $this->validarDatos(
                $datos,
                true
            );

        if (
            $this->usuarioRepository->existeCorreo(
                $datosLimpios['correo']
            )
        ) {
            throw new InvalidArgumentException(
                'Ya existe un usuario registrado con ese correo.'
            );
        }

        if (
            !$this->usuarioRepository->existeRolActivo(
                $datosLimpios['id_rol']
            )
        ) {
            throw new InvalidArgumentException(
                'El rol seleccionado no es válido.'
            );
        }

        $datosLimpios['contrasena_hash'] =
            password_hash(
                $datosLimpios['contrasena'],
                PASSWORD_DEFAULT
            );

        unset($datosLimpios['contrasena']);

        return $this
            ->usuarioRepository
            ->crear(
                $datosLimpios,
                $idComunidad
            );
    }

    public function actualizar(
        int $idUsuario,
        array $datos,
        int $idComunidad
    ): void {
        $usuarioActual = $this->obtener(
            $idUsuario,
            $idComunidad
        );

        if ($usuarioActual === []) {
            throw new RuntimeException(
                'No fue posible localizar el usuario.'
            );
        }

        $datosLimpios =
            $this->validarDatos(
                $datos,
                false
            );

        if (
            $this->usuarioRepository->existeCorreo(
                $datosLimpios['correo'],
                $idUsuario
            )
        ) {
            throw new InvalidArgumentException(
                'Ya existe otro usuario con ese correo.'
            );
        }

        if (
            !$this->usuarioRepository->existeRolActivo(
                $datosLimpios['id_rol']
            )
        ) {
            throw new InvalidArgumentException(
                'El rol seleccionado no es válido.'
            );
        }

        if ($datosLimpios['contrasena'] !== '') {
            $datosLimpios['contrasena_hash'] =
                password_hash(
                    $datosLimpios['contrasena'],
                    PASSWORD_DEFAULT
                );
        } else {
            $datosLimpios['contrasena_hash'] = null;
        }

        unset($datosLimpios['contrasena']);

        $this
            ->usuarioRepository
            ->actualizar(
                $idUsuario,
                $idComunidad,
                $datosLimpios
            );
    }

    public function eliminar(
        int $idUsuario,
        int $idComunidad,
        int $idUsuarioActual
    ): void {
        $this->obtener(
            $idUsuario,
            $idComunidad
        );

        if ($idUsuario === $idUsuarioActual) {
            throw new InvalidArgumentException(
                'No puedes desactivar tu propio usuario mientras tienes la sesión iniciada.'
            );
        }

        $this
            ->usuarioRepository
            ->desactivarEnComunidad(
                $idUsuario,
                $idComunidad
            );
    }

    private function validarDatos(
        array $datos,
        bool $esNuevo
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

        $idRol = filter_var(
            $datos['id_rol'] ?? null,
            FILTER_VALIDATE_INT
        );

        $estado = trim(
            (string) ($datos['estado'] ?? '')
        );

        if (mb_strlen($nombre) < 3) {
            throw new InvalidArgumentException(
                'El nombre debe tener al menos 3 caracteres.'
            );
        }

        if (mb_strlen($nombre) > 120) {
            throw new InvalidArgumentException(
                'El nombre supera el tamaño permitido.'
            );
        }

        if (
            !filter_var(
                $correo,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new InvalidArgumentException(
                'Debes ingresar un correo electrónico válido.'
            );
        }

        if (mb_strlen($correo) > 191) {
            throw new InvalidArgumentException(
                'El correo supera el tamaño permitido.'
            );
        }

        if (mb_strlen($telefono) > 20) {
            throw new InvalidArgumentException(
                'El teléfono supera el tamaño permitido.'
            );
        }

        if (
            $esNuevo
            && strlen($contrasena) < 6
        ) {
            throw new InvalidArgumentException(
                'La contraseña debe tener al menos 6 caracteres.'
            );
        }

        if (
            !$esNuevo
            && $contrasena !== ''
            && strlen($contrasena) < 6
        ) {
            throw new InvalidArgumentException(
                'La nueva contraseña debe tener al menos 6 caracteres.'
            );
        }

        if ($idRol === false || $idRol <= 0) {
            throw new InvalidArgumentException(
                'Debes seleccionar un rol válido.'
            );
        }

        if (
            !in_array(
                $estado,
                ['Activo', 'Inactivo'],
                true
            )
        ) {
            throw new InvalidArgumentException(
                'El estado seleccionado no es válido.'
            );
        }

        return [
            'nombre' => $nombre,
            'correo' => $correo,
            'telefono' =>
                $telefono === ''
                    ? null
                    : $telefono,
            'contrasena' => $contrasena,
            'id_rol' => (int) $idRol,
            'estado' => $estado,
        ];
    }
}