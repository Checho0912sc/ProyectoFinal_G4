<?php

declare(strict_types=1);

final class ActividadService
{
    public function __construct(
        private readonly ActividadRepository $actividadRepository
    ) {
    }

    public function obtenerModulo(
        int $idComunidad,
        ?string $filtroTipo = null
    ): array {
        return [
            'resumen' => [
                'total' =>
                    $this->actividadRepository
                        ->contarPorComunidad($idComunidad),

                'reuniones' =>
                    $this->actividadRepository
                        ->contarPorTipo(
                            $idComunidad,
                            'Reunion'
                        ),

                'eventos' =>
                    $this->actividadRepository
                        ->contarPorTipo(
                            $idComunidad,
                            'Evento'
                        ),

                'jornadas' =>
                    $this->actividadRepository
                        ->contarPorTipo(
                            $idComunidad,
                            'Jornada'
                        ),
            ],

            'listado' =>
                $this->actividadRepository
                    ->listarPorComunidad(
                        $idComunidad,
                        $filtroTipo
                    ),

            'proximas' =>
                $this->actividadRepository
                    ->listarProximas($idComunidad),

            'proyectos' =>
                $this->actividadRepository
                    ->listarProyectosDeComunidad(
                        $idComunidad
                    ),
        ];
    }

    public function registrar(
        int $idComunidad,
        int $idResponsable,
        array $post
    ): void {
        $titulo = trim(
            (string) ($post['titulo'] ?? '')
        );

        $tipo = trim(
            (string) ($post['tipo'] ?? '')
        );

        $fecha = trim(
            (string) ($post['fecha'] ?? '')
        );

        $hora = trim(
            (string) ($post['hora'] ?? '')
        );

        $lugar = trim(
            (string) ($post['lugar'] ?? '')
        );

        $descripcion = trim(
            (string) ($post['descripcion'] ?? '')
        );

        $idProyectoTexto = trim(
            (string) ($post['id_proyecto'] ?? '')
        );

        $idProyecto = null;

        if (strlen($titulo) < 3) {
            throw new InvalidArgumentException(
                'El título debe tener al menos 3 caracteres.'
            );
        }

        $tiposValidos = [
            'Reunion',
            'Evento',
            'Jornada',
            'Proyecto',
        ];

        if (!in_array($tipo, $tiposValidos, true)) {
            throw new InvalidArgumentException(
                'El tipo de actividad no es válido.'
            );
        }

        if ($fecha === '') {
            throw new InvalidArgumentException(
                'La fecha es requerida.'
            );
        }

        if ($hora === '') {
            throw new InvalidArgumentException(
                'La hora es requerida.'
            );
        }

        if ($lugar === '') {
            throw new InvalidArgumentException(
                'El lugar es requerido.'
            );
        }

        if ($idProyectoTexto !== '') {
            $idProyecto = (int) $idProyectoTexto;

            if ($idProyecto <= 0) {
                throw new InvalidArgumentException(
                    'El proyecto seleccionado no es válido.'
                );
            }

            if (
                !$this->actividadRepository
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

        $this->actividadRepository->insertar([
            'id_comunidad' => $idComunidad,
            'id_proyecto' => $idProyecto,
            'id_responsable' => $idResponsable,
            'titulo' => $titulo,
            'tipo' => $tipo,
            'descripcion' => $descripcion,
            'fecha' => $fecha,
            'hora' => $hora,
            'lugar' => $lugar,
        ]);
    }
}