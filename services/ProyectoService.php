<?php

declare(strict_types=1);

final class ProyectoService
{
    private const ESTADOS_PERMITIDOS = [
        'Planificado',
        'En proceso',
        'Pausado',
        'Finalizado',
        'Cancelado',
    ];

    public function __construct(
        private readonly ProyectoRepository $proyectoRepository
    ) {
    }

    public function listar(
        int $idComunidad
    ): array {
        return $this
            ->proyectoRepository
            ->listarPorComunidad($idComunidad);
    }

    public function obtener(
        int $idProyecto,
        int $idComunidad
    ): array {
        if ($idProyecto <= 0) {
            throw new InvalidArgumentException(
                'El proyecto solicitado no es válido.'
            );
        }

        $proyecto = $this
            ->proyectoRepository
            ->buscarPorIdYComunidad(
                $idProyecto,
                $idComunidad
            );

        if ($proyecto === null) {
            throw new RuntimeException(
                'El proyecto no existe en esta comunidad.'
            );
        }

        return $proyecto;
    }

    public function listarGrupos(
        int $idComunidad
    ): array {
        return $this
            ->proyectoRepository
            ->listarGrupos($idComunidad);
    }

    public function listarResponsables(
        int $idComunidad
    ): array {
        return $this
            ->proyectoRepository
            ->listarResponsables($idComunidad);
    }

    public function crear(
        array $datos,
        int $idComunidad
    ): int {
        $datosLimpios =
            $this->validarDatos(
                $datos,
                $idComunidad
            );

        if (
            $this->proyectoRepository
                ->existeNombreEnGrupo(
                    $datosLimpios['nombre'],
                    $datosLimpios['id_grupo']
                )
        ) {
            throw new InvalidArgumentException(
                'Ya existe un proyecto con ese nombre en el grupo seleccionado.'
            );
        }

        $proyecto = new Proyecto(
            null,
            $datosLimpios['id_grupo'],
            $datosLimpios['id_responsable'],
            $datosLimpios['nombre'],
            $datosLimpios['descripcion'],
            $datosLimpios['fecha_inicio'],
            $datosLimpios['fecha_fin'],
            $datosLimpios['estado'],
            $datosLimpios['presupuesto']
        );

        return $this
            ->proyectoRepository
            ->crear($proyecto);
    }

    public function actualizar(
        int $idProyecto,
        array $datos,
        int $idComunidad
    ): void {
        $this->obtener(
            $idProyecto,
            $idComunidad
        );

        $datosLimpios =
            $this->validarDatos(
                $datos,
                $idComunidad
            );

        if (
            $this->proyectoRepository
                ->existeNombreEnGrupo(
                    $datosLimpios['nombre'],
                    $datosLimpios['id_grupo'],
                    $idProyecto
                )
        ) {
            throw new InvalidArgumentException(
                'Ya existe otro proyecto con ese nombre en el grupo seleccionado.'
            );
        }

        $proyecto = new Proyecto(
            $idProyecto,
            $datosLimpios['id_grupo'],
            $datosLimpios['id_responsable'],
            $datosLimpios['nombre'],
            $datosLimpios['descripcion'],
            $datosLimpios['fecha_inicio'],
            $datosLimpios['fecha_fin'],
            $datosLimpios['estado'],
            $datosLimpios['presupuesto']
        );

        $this
            ->proyectoRepository
            ->actualizar($proyecto);
    }

    public function eliminar(
        int $idProyecto,
        int $idComunidad
    ): void {
        $proyecto = $this->obtener(
            $idProyecto,
            $idComunidad
        );

        if (
            $proyecto['estado']
            === 'Cancelado'
        ) {
            throw new InvalidArgumentException(
                'El proyecto ya se encuentra cancelado.'
            );
        }

        $this
            ->proyectoRepository
            ->cancelar($idProyecto);
    }

    private function validarDatos(
        array $datos,
        int $idComunidad
    ): array {
        $idGrupo = filter_var(
            $datos['id_grupo'] ?? null,
            FILTER_VALIDATE_INT
        );

        $idResponsable = filter_var(
            $datos['id_responsable'] ?? null,
            FILTER_VALIDATE_INT
        );

        $nombre = trim(
            (string) ($datos['nombre'] ?? '')
        );

        $descripcion = trim(
            (string) ($datos['descripcion'] ?? '')
        );

        $fechaInicio = trim(
            (string) ($datos['fecha_inicio'] ?? '')
        );

        $fechaFin = trim(
            (string) ($datos['fecha_fin'] ?? '')
        );

        $estado = trim(
            (string) ($datos['estado'] ?? '')
        );

        $presupuesto =
            $datos['presupuesto'] ?? null;

        if (
            $idGrupo === false
            || $idGrupo <= 0
        ) {
            throw new InvalidArgumentException(
                'Debes seleccionar un grupo válido.'
            );
        }

        if (
            !$this->proyectoRepository
                ->existeGrupoEnComunidad(
                    (int) $idGrupo,
                    $idComunidad
                )
        ) {
            throw new InvalidArgumentException(
                'El grupo seleccionado no pertenece a la comunidad actual.'
            );
        }

        if (
            $idResponsable === false
            || $idResponsable <= 0
        ) {
            throw new InvalidArgumentException(
                'Debes seleccionar un responsable válido.'
            );
        }

        if (
            !$this->proyectoRepository
                ->existeResponsableEnComunidad(
                    (int) $idResponsable,
                    $idComunidad
                )
        ) {
            throw new InvalidArgumentException(
                'El responsable seleccionado no pertenece a la comunidad actual.'
            );
        }

        if (mb_strlen($nombre) < 3) {
            throw new InvalidArgumentException(
                'El nombre del proyecto debe tener al menos 3 caracteres.'
            );
        }

        if (mb_strlen($nombre) > 150) {
            throw new InvalidArgumentException(
                'El nombre del proyecto supera el tamaño permitido.'
            );
        }

        if (!$this->fechaValida($fechaInicio)) {
            throw new InvalidArgumentException(
                'La fecha de inicio no es válida.'
            );
        }

        if (
            $fechaFin !== ''
            && !$this->fechaValida($fechaFin)
        ) {
            throw new InvalidArgumentException(
                'La fecha de finalización no es válida.'
            );
        }

        if (
            $fechaFin !== ''
            && $fechaFin < $fechaInicio
        ) {
            throw new InvalidArgumentException(
                'La fecha final no puede ser anterior a la fecha de inicio.'
            );
        }

        if (
            !in_array(
                $estado,
                self::ESTADOS_PERMITIDOS,
                true
            )
        ) {
            throw new InvalidArgumentException(
                'El estado seleccionado no es válido.'
            );
        }

        if (
            !is_numeric($presupuesto)
            || (float) $presupuesto < 0
        ) {
            throw new InvalidArgumentException(
                'El presupuesto debe ser un valor igual o mayor a cero.'
            );
        }

        return [
            'id_grupo' => (int) $idGrupo,

            'id_responsable' =>
                (int) $idResponsable,

            'nombre' => $nombre,

            'descripcion' =>
                $descripcion === ''
                    ? null
                    : $descripcion,

            'fecha_inicio' => $fechaInicio,

            'fecha_fin' =>
                $fechaFin === ''
                    ? null
                    : $fechaFin,

            'estado' => $estado,

            'presupuesto' =>
                (float) $presupuesto,
        ];
    }

    private function fechaValida(
        string $fecha
    ): bool {
        $objetoFecha =
            DateTime::createFromFormat(
                'Y-m-d',
                $fecha
            );

        return $objetoFecha !== false
            && $objetoFecha->format('Y-m-d')
                === $fecha;
    }
}