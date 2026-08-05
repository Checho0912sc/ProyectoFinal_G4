<?php

declare(strict_types=1);

final class DashboardService
{
    public function __construct(
        private readonly DashboardRepository $dashboardRepository
    ) {
    }

    public function obtenerPanel(
        int $idComunidad
    ): array {
        return [
            'resumen' => [
                'proyectos_activos' =>
                    $this->dashboardRepository
                        ->contarProyectosActivos(
                            $idComunidad
                        ),

                'actividades_proximas' =>
                    $this->dashboardRepository
                        ->contarActividadesProximas(
                            $idComunidad
                        ),

                'miembros_activos' =>
                    $this->dashboardRepository
                        ->contarMiembrosActivos(
                            $idComunidad
                        ),

                'saldo' =>
                    $this->dashboardRepository
                        ->obtenerSaldo(
                            $idComunidad
                        ),
            ],

            'proyectos' =>
                $this->dashboardRepository
                    ->obtenerProyectosRecientes(
                        $idComunidad
                    ),

            'actividades' =>
                $this->dashboardRepository
                    ->obtenerActividadesProximas(
                        $idComunidad
                    ),

            'movimientos' =>
                $this->dashboardRepository
                    ->obtenerMovimientosRecientes(
                        $idComunidad
                    ),
        ];
    }
}