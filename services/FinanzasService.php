<?php

declare(strict_types=1);

final class FinanzasService
{
    public function __construct(
        private readonly MovimientoFinancieroRepository $repository
    ) {
    }

    public function obtenerDatos(
        int $idComunidad
    ): array {
        return [
            'resumen' => $this->repository
                ->obtenerResumen($idComunidad),

            'movimientos' => $this->repository
                ->obtenerPorComunidad($idComunidad),

            'proyectos' => $this->repository
                ->obtenerProyectos($idComunidad),
        ];
    }

    public function registrar(
        int $idComunidad,
        int $idUsuario,
        ?int $idProyecto,
        string $tipo,
        string $descripcion,
        float $monto,
        string $fecha
    ): void {
        $tipo = trim($tipo);
        $descripcion = trim($descripcion);
        $fecha = trim($fecha);

        if (
            !in_array(
                $tipo,
                ['Ingreso', 'Egreso'],
                true
            )
        ) {
            throw new InvalidArgumentException(
                'El tipo de movimiento no es válido.'
            );
        }

        if ($descripcion === '') {
            throw new InvalidArgumentException(
                'La descripción es obligatoria.'
            );
        }

        if (mb_strlen($descripcion) > 255) {
            throw new InvalidArgumentException(
                'La descripción no puede superar los 255 caracteres.'
            );
        }

        if ($monto <= 0) {
            throw new InvalidArgumentException(
                'El monto debe ser mayor que cero.'
            );
        }

        $fechaValida = DateTime::createFromFormat(
            'Y-m-d',
            $fecha
        );

        if (
            $fechaValida === false
            || $fechaValida->format('Y-m-d') !== $fecha
        ) {
            throw new InvalidArgumentException(
                'La fecha no es válida.'
            );
        }

        if ($idProyecto !== null) {
            if (
                !$this->repository
                    ->proyectoPerteneceAComunidad(
                        $idProyecto,
                        $idComunidad
                    )
            ) {
                throw new InvalidArgumentException(
                    'El proyecto seleccionado no pertenece a la comunidad actual.'
                );
            }
        }

        $movimiento = new MovimientoFinanciero(
            null,
            $idComunidad,
            $idProyecto,
            $idUsuario,
            $tipo,
            $descripcion,
            $monto,
            $fecha
        );

        $guardado = $this->repository->crear(
            $movimiento
        );

        if (!$guardado) {
            throw new RuntimeException(
                'No fue posible registrar el movimiento.'
            );
        }
    }

    public function anular(
        int $idMovimiento,
        int $idComunidad
    ): void {
        if ($idMovimiento <= 0) {
            throw new InvalidArgumentException(
                'El movimiento indicado no es válido.'
            );
        }

        $movimiento =
            $this->repository->obtenerPorId(
                $idMovimiento,
                $idComunidad
            );

        if ($movimiento === null) {
            throw new InvalidArgumentException(
                'El movimiento no existe o no pertenece a esta comunidad.'
            );
        }

        if ($movimiento['estado'] === 'Anulado') {
            throw new InvalidArgumentException(
                'El movimiento ya se encuentra anulado.'
            );
        }

        $anulado = $this->repository->anular(
            $idMovimiento,
            $idComunidad
        );

        if (!$anulado) {
            throw new RuntimeException(
                'No fue posible anular el movimiento.'
            );
        }
    }
}