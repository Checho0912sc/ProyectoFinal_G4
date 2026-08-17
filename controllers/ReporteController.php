<?php

declare(strict_types=1);

final class ReporteController extends Controller
{
    public function index(): void
    {
        Auth::exigirLogin();

        $usuarioActual = Auth::usuario();

        $idComunidad =
            (int) $usuarioActual['id_comunidad'];

        $repository =
            new ReporteRepository(
                Database::getConnection()
            );

        $service =
            new ReporteService($repository);

        $datos =
            $service->obtenerDatos(
                $idComunidad
            );

        $this->render(
            'reportes/index',
            [
                'titulo' => 'Reportes',
                'usuarioActual' => $usuarioActual,
                'indicadores' =>
                    $datos['indicadores'],
                'proyectos' =>
                    $datos['proyectos'],
                'financiero' =>
                    $datos['financiero'],
                'actividades' =>
                    $datos['actividades'],
            ]
        );
    }
}