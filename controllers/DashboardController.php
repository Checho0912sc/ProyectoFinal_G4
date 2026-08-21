<?php

declare(strict_types=1);

final class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::exigirLogin();

        $usuarioActual = Auth::usuario();

        $repositorio = new DashboardRepository(
            Database::getConnection()
        );

        $servicio = new DashboardService(
            $repositorio
        );

        $panel = $servicio->obtenerPanel(
            (int) $usuarioActual['id_comunidad']
        );

        $error = Auth::flash('error');

        $this->render('dashboard/index', [
            'titulo' => 'Dashboard',

            'usuarioActual' => $usuarioActual,

            'resumen' => $panel['resumen'],

            'proyectos' => $panel['proyectos'],

            'actividades' => $panel['actividades'],

            'movimientos' => $panel['movimientos'],
            
            'error' => $error,
        ]);
    }
}