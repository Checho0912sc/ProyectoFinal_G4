<?php

declare(strict_types=1);

final class ComunidadService
{
    public function __construct(
        private readonly ComunidadRepository $comunidadRepository
    ) {
    }

    public function listarActivas(): array {
        return $this
            ->comunidadRepository
            ->listarActivas();
    }

    public function crear(
        int $idUsuario,
        array $datos
    ): array {
        $nombre = trim(
            (string) (
                $datos['nombre'] ?? ''
            )
        );

        $descripcion = trim(
            (string) (
                $datos['descripcion'] ?? ''
            )
        );

        if ($idUsuario <= 0) {
            throw new InvalidArgumentException(
                'El usuario no es válido.'
            );
        }

        if (
            $this->comunidadRepository
                ->tieneComunidadActiva(
                    $idUsuario
                )
        ) {
            throw new InvalidArgumentException(
                'Ya perteneces a una comunidad.'
            );
        }

        if (mb_strlen($nombre) < 3) {
            throw new InvalidArgumentException(
                'El nombre debe tener al menos 3 caracteres.'
            );
        }

        if (mb_strlen($nombre) > 120) {
            throw new InvalidArgumentException(
                'El nombre de la comunidad es demasiado largo.'
            );
        }

        if (
            $this->comunidadRepository
                ->existeNombre($nombre)
        ) {
            throw new InvalidArgumentException(
                'Ya existe una comunidad con ese nombre.'
            );
        }

        return $this
            ->comunidadRepository
            ->crearConAdministrador(
                $idUsuario,
                $nombre,
                $descripcion === ''
                    ? null
                    : $descripcion
            );
    }

    public function unirse(
        int $idUsuario,
        int $idComunidad
    ): array {
        if (
            $idUsuario <= 0
            || $idComunidad <= 0
        ) {
            throw new InvalidArgumentException(
                'La comunidad seleccionada no es válida.'
            );
        }

        $comunidad = $this
            ->comunidadRepository
            ->buscarActivaPorId(
                $idComunidad
            );

        if ($comunidad === null) {
            throw new InvalidArgumentException(
                'La comunidad seleccionada no existe o está inactiva.'
            );
        }

        $membresia = $this
            ->comunidadRepository
            ->obtenerMembresiaActiva(
                $idUsuario,
                $idComunidad
            );

        if ($membresia !== null) {
            return $membresia;
        }

        if (
            $this->comunidadRepository
                ->tieneComunidadActiva(
                    $idUsuario
                )
        ) {
            throw new InvalidArgumentException(
                'Ya perteneces a una comunidad.'
            );
        }

        $this
            ->comunidadRepository
            ->unirUsuario(
                $idUsuario,
                $idComunidad
            );

        $membresia = $this
            ->comunidadRepository
            ->obtenerMembresiaActiva(
                $idUsuario,
                $idComunidad
            );

        if ($membresia === null) {
            throw new RuntimeException(
                'No fue posible unirse a la comunidad.'
            );
        }

        return $membresia;
    }

    // ------------------ ABANDONAR UNA COMUNIDAD ----------------------    
    public function abandonar(
        int $idUsuario,
        int $idComunidad
    ): void {
        if (
            $idUsuario <= 0
            || $idComunidad <= 0
        ) {
            throw new InvalidArgumentException(
                'Los datos de la comunidad no son válidos.'
            );
        }

        $membresia = $this
            ->comunidadRepository
            ->obtenerMembresiaActiva(
                $idUsuario,
                $idComunidad
            );

        if ($membresia === null) {
            throw new InvalidArgumentException(
                'No perteneces a esta comunidad.'
            );
        }

        $cantidadMiembros = $this
            ->comunidadRepository
            ->contarMiembrosActivos(
                $idComunidad
            );

        $cantidadAdministradores = $this
            ->comunidadRepository
            ->contarAdministradoresActivos(
                $idComunidad
            );

        $esAdministrador =
            $membresia['rol']
            === 'Administrador';

        if (
            $esAdministrador
            && $cantidadAdministradores === 1
            && $cantidadMiembros > 1
        ) {
            throw new InvalidArgumentException(
                'Debes asignar otro administrador antes de salir de la comunidad.'
            );
        }

        $desactivarComunidad =
            $cantidadMiembros === 1;

        $this
            ->comunidadRepository
            ->abandonar(
                $idUsuario,
                $idComunidad,
                $desactivarComunidad
            );
    }
}