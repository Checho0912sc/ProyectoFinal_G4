<?php

declare(strict_types=1);

final class ReporteService
{
    public function __construct(
        private readonly ReporteRepository $repository
    ) {
    }

    public function obtenerDatos(
        int $idComunidad
    ): array {
        return [
            'indicadores' => [
                'proyectos' =>
                    $this->repository
                        ->contarProyectos(
                            $idComunidad
                        ),

                'actividades' =>
                    $this->repository
                        ->contarActividades(
                            $idComunidad
                        ),

                'ingresos' =>
                    $this->repository
                        ->obtenerTotalIngresos(
                            $idComunidad
                        ),

                'usuarios' =>
                    $this->repository
                        ->contarUsuariosActivos(
                            $idComunidad
                        ),
            ],

            'proyectos' =>
                $this->repository
                    ->obtenerReporteProyectos(
                        $idComunidad
                    ),

            'financiero' =>
                $this->repository
                    ->obtenerReporteFinanciero(
                        $idComunidad
                    ),

            'actividades' =>
                $this->repository
                    ->obtenerReporteActividades(
                        $idComunidad
                    ),
        ];
    }
}