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


    // ------------------ ACTUALIZAR MEMBRESÍA (Solamente cambia el rol y el estado) ------------------

    public function actualizar(
        int $idUsuario,
        array $datos,
        int $idComunidad,
        int $idUsuarioActual
    ): void {
        $this->obtener(
            $idUsuario,
            $idComunidad
        );

        if ($idUsuario === $idUsuarioActual) {
            throw new InvalidArgumentException(
                'No puedes modificar tu propio rol o estado mientras tienes la sesión iniciada.'
            );
        }

        $datosMembresia =
            $this->validarMembresia(
                $datos
            );

        if (
            !$this
                ->usuarioRepository
                ->existeRolActivo(
                    $datosMembresia['id_rol']
                )
        ) {
            throw new InvalidArgumentException(
                'El rol seleccionado no es válido.'
            );
        }

        $this
            ->usuarioRepository
            ->actualizar(
                $idUsuario,
                $idComunidad,
                $datosMembresia
            );
    }


    // ------------------ DESACTIVAR MEMBRESÍA (No elimina la cuenta del usuario) ------------------

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
                'No puedes desactivar tu propia membresía mientras tienes la sesión iniciada.'
            );
        }

        $this
            ->usuarioRepository
            ->desactivarEnComunidad(
                $idUsuario,
                $idComunidad
            );
    }


    // ------------------ VALIDAR MEMBRESÍA (Revisa el rol y el estado recibidos) ------------------

    private function validarMembresia(
        array $datos
    ): array {
        $idRol = filter_var(
            $datos['id_rol'] ?? null,
            FILTER_VALIDATE_INT
        );

        $estado = trim(
            (string) ($datos['estado'] ?? '')
        );

        if (
            $idRol === false
            || $idRol === null
            || $idRol <= 0
        ) {
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
            'id_rol' => (int) $idRol,
            'estado' => $estado,
        ];
    }
}